<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\JobApplicant;
use App\Models\JobCategory;
use App\Models\JobListing;
use App\Models\Membership;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Job Applicants (tenant-scoped). Two read views over the same table:
 *   index()      → completed applications  (final_submit = 1) → "Job Applicant"
 *   incomplete() → unfinished drafts        (final_submit = 0) → "Incomplete Record"
 * Gated by the applicants module.
 */
class JobApplicantController extends Controller
{
    public function index(Request $request): View
    {
        return $this->listView($request, finalSubmit: true, mode: 'complete');
    }

    public function incomplete(Request $request): View
    {
        return $this->listView($request, finalSubmit: false, mode: 'incomplete');
    }

    /** Autocomplete for the Location filter — office names from the pincode list. */
    public function locations(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if (strlen($q) < 2 || ! \Illuminate\Support\Facades\Schema::hasTable('pincode_list')) {
            return response()->json([]);
        }

        $rows = \Illuminate\Support\Facades\DB::table('pincode_list')
            ->where('officename', 'like', $q.'%')
            ->orderBy('officename')
            ->limit(20)
            ->get(['officename', 'district', 'statename', 'pincode']);

        return response()->json($rows->map(fn ($r) => [
            'name' => $r->officename,
            'district' => $r->district,
            'state' => $r->statename,
            'pincode' => $r->pincode,
        ])->values());
    }

    public function show(Request $request, JobApplicant $jobApplicant): View
    {
        $company = $this->company($request);
        $this->ensureOwned($company, $jobApplicant);

        $assigned = $this->assignedDepartments($request, $company);
        $this->ensureInScope($jobApplicant, $assigned);

        [$prev, $next] = $this->neighbours($company, $jobApplicant, $assigned);

        return view('company.job_applicants.show', [
            'company' => $company,
            'applicant' => $jobApplicant->load(['listing.category', 'comments.author']),
            'mentionables' => $this->companyUsers($company),
            'statuses' => JobApplicant::statuses($company->id),
            'statusColors' => JobApplicant::statusColors($company->id),
            'prev' => $prev,
            'next' => $next,
        ]);
    }

    /**
     * Manually add an applicant (HR entry). The chosen designation must be
     * within the user's department scope. Saved as a completed application so
     * it appears under Job Applicants (not Incomplete Records).
     */
    public function store(Request $request): RedirectResponse
    {
        $company = $this->company($request);
        $assigned = $this->assignedDepartments($request, $company);
        $statuses = JobApplicant::statuses($company->id);

        $data = $request->validate([
            'job_listing_id' => ['required', Rule::exists('job_listings', 'id')->where('tenant_id', $company->id)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'gender' => ['nullable', Rule::in(['Male', 'Female', 'Other'])],
            // Address is composed from these parts (street + location + city/state/pincode/country).
            'address_line' => ['nullable', 'string', 'max:500'],
            'location_name' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'pincode' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'source' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', Rule::in(array_keys($statuses))],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
            'cover_letter' => ['nullable', 'string'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'resume' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'education' => ['nullable', 'array'],
            'education.*.school' => ['nullable', 'string', 'max:255'],
            'education.*.program' => ['nullable', 'string', 'max:255'],
            'education.*.startDate' => ['nullable', 'string', 'max:50'],
            'education.*.endDate' => ['nullable', 'string', 'max:50'],
            'work_experience' => ['nullable', 'array'],
            'work_experience.*.company' => ['nullable', 'string', 'max:255'],
            'work_experience.*.position' => ['nullable', 'string', 'max:255'],
            'work_experience.*.startDate' => ['nullable', 'string', 'max:50'],
            'work_experience.*.endDate' => ['nullable', 'string', 'max:50'],
        ]);

        // Department scope: the chosen designation must be one the user may access.
        $listing = JobListing::where('tenant_id', $company->id)->findOrFail($data['job_listing_id']);
        if ($assigned !== null) {
            abort_unless(in_array($listing->job_category_id, $assigned, true), 403);
        }

        $address = $this->composeAddress($data);

        $applicant = JobApplicant::create([
            'tenant_id' => $company->id,
            'job_listing_id' => $listing->id,
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'gender' => $data['gender'] ?? null,
            'address' => $address,
            'source' => $data['source'] ?? 'Manual',
            'position' => $data['position'] ?: $listing->job_role,
            'portfolio_url' => $data['portfolio_url'] ?? null,
            'cover_letter' => $data['cover_letter'] ?? null,
            'profile_image' => $this->uploadFile($request, 'profile_image', 'profile_images'),
            'resume' => $this->uploadFile($request, 'resume', 'resumes'),
            'education' => $this->cleanRows($data['education'] ?? []),
            'work_experience' => $this->cleanRows($data['work_experience'] ?? []),
            'final_submit' => true,
            'status' => $data['status'] ?? (array_key_exists('new', $statuses) ? 'new' : array_key_first($statuses)),
        ]);

        ActivityLog::record('applicant_added', "Added applicant {$applicant->name}", [
            'subject_type' => JobApplicant::class,
            'subject_id' => $applicant->id,
        ]);

        return redirect()->route('company.applicants.index')->with('sweetalert', [
            'icon' => 'success', 'title' => 'Applicant added', 'text' => $applicant->name,
        ]);
    }

    /** Build a single display address from the structured parts. */
    private function composeAddress(array $data): ?string
    {
        $parts = array_filter([
            $data['address_line'] ?? null,
            $data['location_name'] ?? null,
            $data['city'] ?? null,
            $data['state'] ?? null,
            $data['pincode'] ?? null,
            $data['country'] ?? null,
        ], fn ($v) => filled($v));

        return $parts ? implode(', ', $parts) : null;
    }

    /** Drop repeatable rows that are entirely empty; return null when nothing left. */
    private function cleanRows(array $rows): ?array
    {
        $clean = array_values(array_filter($rows, fn ($row) => collect($row)->filter(fn ($v) => filled($v))->isNotEmpty()));

        return $clean ?: null;
    }

    /** Move an uploaded file into public/{dir} and return the stored filename. */
    private function uploadFile(Request $request, string $field, string $dir): ?string
    {
        if (! $request->hasFile($field)) {
            return null;
        }

        $file = $request->file($field);
        $name = time().random_int(10, 999).'.'.$file->getClientOriginalExtension();
        $file->move(public_path($dir), $name);

        return $name;
    }

    public function updateStatus(Request $request, JobApplicant $jobApplicant): RedirectResponse
    {
        $company = $this->company($request);
        $this->ensureOwned($company, $jobApplicant);

        $statuses = JobApplicant::statuses($company->id);

        $data = $request->validate([
            'status' => ['required', 'string', Rule::in(array_keys($statuses))],
        ]);

        $from = $jobApplicant->status;
        $jobApplicant->update(['status' => $data['status']]);

        ActivityLog::record('status_change',
            "Changed {$jobApplicant->name} status: {$from} → {$data['status']}", [
                'subject_type' => JobApplicant::class,
                'subject_id' => $jobApplicant->id,
            ]);

        return back()->with('sweetalert', [
            'icon' => 'success', 'title' => 'Status updated',
            'text' => $statuses[$data['status']] ?? $data['status'],
        ]);
    }

    public function destroy(Request $request, JobApplicant $jobApplicant): RedirectResponse
    {
        $company = $this->company($request);
        $this->ensureOwned($company, $jobApplicant);

        $email = $jobApplicant->email;
        $jobApplicant->delete();

        return back()->with('sweetalert', ['icon' => 'success', 'title' => 'Applicant removed', 'text' => $email]);
    }

    private function listView(Request $request, bool $finalSubmit, string $mode): View
    {
        $company = $this->company($request);
        $assigned = $this->assignedDepartments($request, $company);

        $departments = JobCategory::where('tenant_id', $company->id)
            ->when($assigned !== null, fn ($q) => $q->whereIn('id', $assigned))
            ->orderBy('name')->get();

        $listings = JobListing::where('tenant_id', $company->id)
            ->when($assigned !== null, fn ($q) => $q->whereIn('job_category_id', $assigned))
            ->orderBy('job_role')->get();

        return view('company.job_applicants.index', [
            'company' => $company,
            'applicants' => $this->applicants($request, $company, $finalSubmit, $assigned),
            'departments' => $departments,
            'listings' => $listings,
            'statuses' => JobApplicant::statuses($company->id),
            'statusColors' => JobApplicant::statusColors($company->id),
            'mode' => $mode,
            'filters' => $request->only(['department', 'job_title', 'location', 'gender', 'from_date', 'to_date', 'search', 'answered', 'education']),
            'educationPrograms' => \Illuminate\Support\Facades\Schema::hasTable('education_programs')
                ? \Illuminate\Support\Facades\DB::table('education_programs')->orderBy('program_name')->pluck('program_name')
                : collect(),
        ]);
    }

    private function company(Request $request): Tenant
    {
        return $request->user()->company();
    }

    private function applicants(Request $request, Tenant $company, bool $finalSubmit, ?array $assigned)
    {
        $query = JobApplicant::where('tenant_id', $company->id)
            ->where('final_submit', $finalSubmit)
            ->with('listing');

        // Department-restricted users only see applicants in their assigned departments.
        $query->when($assigned !== null, fn ($q) => $q->whereHas('listing',
            fn ($l) => $l->whereIn('job_category_id', $assigned)));

        $query->when($request->filled('job_title'), fn ($q) => $q->where('job_listing_id', $request->job_title));
        $query->when($request->filled('gender'), fn ($q) => $q->where('gender', $request->gender));
        $query->when($request->filled('from_date'), fn ($q) => $q->whereDate('created_at', '>=', $request->from_date));
        $query->when($request->filled('to_date'), fn ($q) => $q->whereDate('created_at', '<=', $request->to_date));

        $query->when($request->filled('department'), fn ($q) => $q->whereHas('listing',
            fn ($l) => $l->where('job_category_id', $request->department)));

        // Location: match the applicant's address against the chosen office name.
        // Office names carry suffixes ("Nerul B.O") while addresses hold just the
        // locality ("Nerul, Navi Mumbai"), so we also match on the first token.
        $query->when($request->filled('location'), function ($q) use ($request) {
            $loc = trim($request->location);
            $core = strtok($loc, ' ,');
            $q->where(function ($w) use ($loc, $core) {
                $w->where('address', 'like', '%'.$loc.'%');
                if ($core && strlen($core) >= 3 && $core !== $loc) {
                    $w->orWhere('address', 'like', '%'.$core.'%');
                }
            });
        });

        // Education: match the chosen program against the applicant's education JSON.
        $query->when($request->filled('education'), fn ($q) => $q->where('education', 'like', '%'.$request->education.'%'));

        // Answered Questions: Yes = applicant submitted answers, No = none.
        $query->when($request->input('answered') === 'yes', fn ($q) => $q->whereRaw('JSON_LENGTH(answers) > 0'));
        $query->when($request->input('answered') === 'no', fn ($q) => $q->where(
            fn ($w) => $w->whereNull('answers')->orWhereRaw('JSON_LENGTH(answers) = 0')
        ));

        $query->when($request->filled('search'), function ($q) use ($request) {
            $term = '%'.$request->search.'%';
            $q->where(fn ($w) => $w->where('name', 'like', $term)
                ->orWhere('email', 'like', $term)
                ->orWhere('phone', 'like', $term));
        });

        return $query->latest()->get();
    }

    private function ensureOwned(Tenant $company, JobApplicant $applicant): void
    {
        abort_unless($applicant->tenant_id === $company->id, 404);
    }

    /** Prev/next applicant in the same list (same final_submit group), latest-first. */
    private function neighbours(Tenant $company, JobApplicant $applicant, ?array $assigned): array
    {
        $ids = JobApplicant::where('tenant_id', $company->id)
            ->where('final_submit', $applicant->final_submit)
            ->when($assigned !== null, fn ($q) => $q->whereHas('listing',
                fn ($l) => $l->whereIn('job_category_id', $assigned)))
            ->latest()->pluck('id')->all();

        $i = array_search($applicant->id, $ids, true);

        return [
            $i > 0 ? $ids[$i - 1] : null,
            ($i !== false && $i < count($ids) - 1) ? $ids[$i + 1] : null,
        ];
    }

    /**
     * Departments the logged-in user is restricted to (their membership's
     * job_category_ids). Returns null when the user is unrestricted (no
     * departments assigned) — e.g. the company Admin — meaning "see everything".
     */
    private function assignedDepartments(Request $request, Tenant $company): ?array
    {
        $membership = Membership::where('tenant_id', $company->id)
            ->where('user_id', $request->user()->id)
            ->first();

        $ids = $membership?->job_category_ids ?? [];

        return empty($ids) ? null : array_values($ids);
    }

    /** A restricted user may only open an applicant within their departments. */
    private function ensureInScope(JobApplicant $applicant, ?array $assigned): void
    {
        if ($assigned === null) {
            return;
        }

        abort_unless(
            $applicant->listing && in_array($applicant->listing->job_category_id, $assigned, true),
            404
        );
    }

    /** Company users available to @mention in the Activity Chat. */
    private function companyUsers(Tenant $company)
    {
        return $company->memberships()->with('user')->get()
            ->map->user
            ->filter()
            ->map(fn ($u) => ['id' => $u->id, 'name' => $u->name])
            ->values();
    }
}

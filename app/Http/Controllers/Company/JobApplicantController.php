<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\JobApplicant;
use App\Models\JobCategory;
use App\Models\JobListing;
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

    public function show(Request $request, JobApplicant $jobApplicant): View
    {
        $company = $this->company($request);
        $this->ensureOwned($company, $jobApplicant);

        [$prev, $next] = $this->neighbours($company, $jobApplicant);

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

        return view('company.job_applicants.index', [
            'company' => $company,
            'applicants' => $this->applicants($request, $company, $finalSubmit),
            'departments' => JobCategory::where('tenant_id', $company->id)->orderBy('name')->get(),
            'listings' => JobListing::where('tenant_id', $company->id)->orderBy('job_role')->get(),
            'statuses' => JobApplicant::statuses($company->id),
            'statusColors' => JobApplicant::statusColors($company->id),
            'mode' => $mode,
            'filters' => $request->only(['department', 'job_title', 'location', 'gender', 'from_date', 'to_date', 'search']),
        ]);
    }

    private function company(Request $request): Tenant
    {
        return $request->user()->company();
    }

    private function applicants(Request $request, Tenant $company, bool $finalSubmit)
    {
        $query = JobApplicant::where('tenant_id', $company->id)
            ->where('final_submit', $finalSubmit)
            ->with('listing');

        $query->when($request->filled('job_title'), fn ($q) => $q->where('job_listing_id', $request->job_title));
        $query->when($request->filled('gender'), fn ($q) => $q->where('gender', $request->gender));
        $query->when($request->filled('from_date'), fn ($q) => $q->whereDate('created_at', '>=', $request->from_date));
        $query->when($request->filled('to_date'), fn ($q) => $q->whereDate('created_at', '<=', $request->to_date));

        $query->when($request->filled('department'), fn ($q) => $q->whereHas('listing',
            fn ($l) => $l->where('job_category_id', $request->department)));
        $query->when($request->filled('location'), fn ($q) => $q->whereHas('listing',
            fn ($l) => $l->where('location', 'like', '%'.$request->location.'%')));

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
    private function neighbours(Tenant $company, JobApplicant $applicant): array
    {
        $ids = JobApplicant::where('tenant_id', $company->id)
            ->where('final_submit', $applicant->final_submit)
            ->latest()->pluck('id')->all();

        $i = array_search($applicant->id, $ids, true);

        return [
            $i > 0 ? $ids[$i - 1] : null,
            ($i !== false && $i < count($ids) - 1) ? $ids[$i + 1] : null,
        ];
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

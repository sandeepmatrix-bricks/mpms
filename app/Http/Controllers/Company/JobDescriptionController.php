<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Company\Concerns\ScopesToDepartments;
use App\Http\Controllers\Controller;
use App\Models\JobDescription;
use App\Models\JobListing;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Job Management → Job Descriptions (the reference's job_details). The detailed
 * posting attached to a designation (JobListing), including dynamic application
 * questions. Tenant-scoped and gated by the jobs module. Department-restricted
 * users only see and manage descriptions within their assigned departments.
 */
class JobDescriptionController extends Controller
{
    use ScopesToDepartments;

    public function index(Request $request): View
    {
        $company = $this->company($request);
        $assigned = $this->assignedDepartments($request, $company);

        $descriptions = JobDescription::where('tenant_id', $company->id)
            ->when($assigned !== null, fn ($q) => $q->whereHas('listing',
                fn ($l) => $l->whereIn('job_category_id', $assigned)))
            ->with('listing')
            ->latest()
            ->get();

        return view('company.job_descriptions.index', compact('company', 'descriptions'));
    }

    public function create(Request $request): View
    {
        $company = $this->company($request);

        return view('company.job_descriptions.create', [
            'company' => $company,
            'description' => new JobDescription,
            'listings' => $this->listings($company, $this->assignedDepartments($request, $company)),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $company = $this->company($request);
        $data = $this->validated($request, $company);
        $this->ensureListingInScope($company, $this->assignedDepartments($request, $company), $data['job_listing_id']);
        $data['banner_image'] = $this->uploadBanner($request);

        JobDescription::create($this->payload($company, $data));

        return redirect()->route('company.job-descriptions.index')->with('sweetalert', [
            'icon' => 'success', 'title' => 'Job description created', 'text' => $data['designation'] ?? '',
        ]);
    }

    public function edit(Request $request, JobDescription $jobDescription): View
    {
        $company = $this->company($request);
        $this->ensureOwned($company, $jobDescription);
        $assigned = $this->assignedDepartments($request, $company);
        $this->ensureListingInScope($company, $assigned, $jobDescription->job_listing_id);

        return view('company.job_descriptions.edit', [
            'company' => $company,
            'description' => $jobDescription,
            'listings' => $this->listings($company, $assigned),
        ]);
    }

    public function update(Request $request, JobDescription $jobDescription): RedirectResponse
    {
        $company = $this->company($request);
        $this->ensureOwned($company, $jobDescription);
        $assigned = $this->assignedDepartments($request, $company);
        $this->ensureListingInScope($company, $assigned, $jobDescription->job_listing_id);

        $data = $this->validated($request, $company);
        $this->ensureListingInScope($company, $assigned, $data['job_listing_id']);
        $data['banner_image'] = $this->uploadBanner($request) ?? $jobDescription->banner_image;
        $jobDescription->update($this->payload($company, $data));

        return redirect()->route('company.job-descriptions.index')->with('sweetalert', [
            'icon' => 'success', 'title' => 'Job description updated', 'text' => $data['designation'] ?? '',
        ]);
    }

    public function destroy(Request $request, JobDescription $jobDescription): RedirectResponse
    {
        $company = $this->company($request);
        $this->ensureOwned($company, $jobDescription);
        $this->ensureListingInScope($company, $this->assignedDepartments($request, $company), $jobDescription->job_listing_id);

        $jobDescription->delete();

        return back()->with('sweetalert', ['icon' => 'success', 'title' => 'Job description deleted', 'text' => '']);
    }

    private function company(Request $request): Tenant
    {
        return $request->user()->company();
    }

    private function listings(Tenant $company, ?array $assigned = null)
    {
        return JobListing::where('tenant_id', $company->id)
            ->where('status', 'active')
            ->when($assigned !== null, fn ($q) => $q->whereIn('job_category_id', $assigned))
            ->orderBy('job_role')
            ->get();
    }

    /** 404 unless the designation (and therefore its department) is within scope. */
    private function ensureListingInScope(Tenant $company, ?array $assigned, ?string $listingId): void
    {
        if ($assigned === null) {
            return;
        }

        $categoryId = JobListing::withTrashed()
            ->where('tenant_id', $company->id)
            ->whereKey($listingId)
            ->value('job_category_id');

        $this->ensureDepartmentInScope($assigned, $categoryId);
    }

    private function ensureOwned(Tenant $company, JobDescription $description): void
    {
        abort_unless($description->tenant_id === $company->id, 404);
    }

    private function uploadBanner(Request $request): ?string
    {
        if (! $request->hasFile('banner_image')) {
            return null;
        }

        $file = $request->file('banner_image');
        $name = time().random_int(10, 999).'.'.$file->getClientOriginalExtension();
        $file->move(public_path('uploads/careers'), $name);

        return $name;
    }

    /** Build the saveable attributes, including normalised dynamic questions. */
    private function payload(Tenant $company, array $data): array
    {
        $questions = [];
        foreach ($data['job_questions'] ?? [] as $q) {
            if (empty($q['question'])) {
                continue;
            }
            $type = $q['type'] ?? 'text';
            $questions[] = [
                'question' => $q['question'],
                'type' => $type,
                'options' => in_array($type, ['dropdown', 'radio'], true) ? ($q['options'] ?? '') : null,
                'required' => ! empty($q['required']) ? 1 : 0,
            ];
        }

        return [
            'tenant_id' => $company->id,
            'job_listing_id' => $data['job_listing_id'],
            'banner_heading' => $data['banner_heading'] ?? null,
            'banner_image' => $data['banner_image'] ?? null,
            'section_heading' => $data['section_heading'] ?? null,
            'designation' => $data['designation'] ?? null,
            'location' => $data['location'] ?? null,
            'experience_required' => $data['experience_required'] ?? null,
            'working_days' => $data['working_days'] ?? null,
            'job_type' => $data['job_type'] ?? null,
            'reporting_to' => $data['reporting_to'] ?? null,
            'about_role' => $data['about_role'] ?? null,
            'key_responsibilities' => $data['key_responsibilities'] ?? null,
            'key_skills_competencies' => $data['key_skills_competencies'] ?? null,
            'success_looks' => $data['success_looks'] ?? null,
            'qualification' => $data['qualification'] ?? null,
            'job_questions' => $questions ?: null,
            'status' => $data['status'],
        ];
    }

    private function validated(Request $request, Tenant $company): array
    {
        return $request->validate([
            'job_listing_id' => ['required', Rule::exists('job_listings', 'id')->where('tenant_id', $company->id)],
            'banner_heading' => ['nullable', 'string', 'max:255'],
            'banner_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'section_heading' => ['nullable', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'experience_required' => ['nullable', 'string', 'max:255'],
            'working_days' => ['nullable', 'string', 'max:255'],
            'job_type' => ['nullable', 'string', 'max:255'],
            'reporting_to' => ['nullable', 'string', 'max:255'],
            'about_role' => ['nullable', 'string'],
            'key_responsibilities' => ['nullable', 'string'],
            'key_skills_competencies' => ['nullable', 'string'],
            'success_looks' => ['nullable', 'string'],
            'qualification' => ['nullable', 'string'],
            'job_questions' => ['nullable', 'array'],
            'job_questions.*.question' => ['nullable', 'string', 'max:500'],
            'job_questions.*.type' => ['nullable', 'in:text,dropdown,radio'],
            'job_questions.*.options' => ['nullable', 'string', 'max:1000'],
            'job_questions.*.required' => ['nullable', 'in:0,1'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\JobCategory;
use App\Models\JobListing;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Job Management → Designations (the reference's career_category_listing).
 * A designation is a job role posted under a department. Tenant-scoped and
 * gated by the jobs module (read/write/edit/delete).
 */
class JobListingController extends Controller
{
    public function index(Request $request): View
    {
        $company = $this->company($request);

        $listings = JobListing::where('tenant_id', $company->id)
            ->with('category')
            ->latest()
            ->get();

        return view('company.job_listings.index', compact('company', 'listings'));
    }

    public function create(Request $request): View
    {
        $company = $this->company($request);

        return view('company.job_listings.create', [
            'company' => $company,
            'listing' => new JobListing,
            'categories' => $this->categories($company),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $company = $this->company($request);
        $data = $this->validated($request, $company);

        JobListing::create([
            'tenant_id' => $company->id,
            'job_category_id' => $data['job_category_id'],
            'job_role' => $data['job_role'],
            'slug' => Str::slug($data['job_role']),
            'banner_heading' => $data['banner_heading'] ?? null,
            'section_heading' => $data['section_heading'] ?? null,
            'location' => $data['location'] ?? null,
            'banner_image' => $this->uploadBanner($request),
            'status' => $data['status'],
        ]);

        return redirect()->route('company.job-listings.index')->with('sweetalert', [
            'icon' => 'success', 'title' => 'Designation created', 'text' => $data['job_role'],
        ]);
    }

    public function edit(Request $request, JobListing $jobListing): View
    {
        $company = $this->company($request);
        $this->ensureOwned($company, $jobListing);

        return view('company.job_listings.edit', [
            'company' => $company,
            'listing' => $jobListing,
            'categories' => $this->categories($company),
        ]);
    }

    public function update(Request $request, JobListing $jobListing): RedirectResponse
    {
        $company = $this->company($request);
        $this->ensureOwned($company, $jobListing);

        $data = $this->validated($request, $company);

        $jobListing->update([
            'job_category_id' => $data['job_category_id'],
            'job_role' => $data['job_role'],
            'slug' => Str::slug($data['job_role']),
            'banner_heading' => $data['banner_heading'] ?? null,
            'section_heading' => $data['section_heading'] ?? null,
            'location' => $data['location'] ?? null,
            'banner_image' => $this->uploadBanner($request) ?? $jobListing->banner_image,
            'status' => $data['status'],
        ]);

        return redirect()->route('company.job-listings.index')->with('sweetalert', [
            'icon' => 'success', 'title' => 'Designation updated', 'text' => $data['job_role'],
        ]);
    }

    public function destroy(Request $request, JobListing $jobListing): RedirectResponse
    {
        $company = $this->company($request);
        $this->ensureOwned($company, $jobListing);

        $role = $jobListing->job_role;
        $jobListing->delete();

        return back()->with('sweetalert', ['icon' => 'success', 'title' => 'Designation deleted', 'text' => $role]);
    }

    private function company(Request $request): Tenant
    {
        return $request->user()->company();
    }

    private function categories(Tenant $company)
    {
        return JobCategory::where('tenant_id', $company->id)->where('status', 'active')->orderBy('name')->get();
    }

    private function ensureOwned(Tenant $company, JobListing $listing): void
    {
        abort_unless($listing->tenant_id === $company->id, 404);
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

    private function validated(Request $request, Tenant $company): array
    {
        return $request->validate([
            'job_category_id' => ['required', Rule::exists('job_categories', 'id')->where('tenant_id', $company->id)],
            'job_role' => ['required', 'string', 'max:255'],
            'banner_heading' => ['nullable', 'string', 'max:255'],
            'section_heading' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'banner_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);
    }
}

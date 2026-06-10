<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Company\Concerns\ScopesToDepartments;
use App\Http\Controllers\Controller;
use App\Models\JobCategory;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Job Departments (career categories) for a company. Tenant-scoped: a company
 * only ever sees and manages its own departments. Gated by the job_categories
 * module (read/write/edit/delete). Department-restricted users only see and
 * manage the departments assigned to them.
 */
class JobCategoryController extends Controller
{
    use ScopesToDepartments;

    public function index(Request $request): View
    {
        $company = $this->company($request);
        $assigned = $this->assignedDepartments($request, $company);

        $categories = JobCategory::where('tenant_id', $company->id)
            ->when($assigned !== null, fn ($q) => $q->whereIn('id', $assigned))
            ->orderBy('name')
            ->get();

        return view('company.job_categories.index', compact('company', 'categories'));
    }

    public function create(Request $request): View
    {
        return view('company.job_categories.create', [
            'company' => $this->company($request),
            'category' => new JobCategory,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $company = $this->company($request);
        $data = $this->validated($request, $company);

        JobCategory::create([
            'tenant_id' => $company->id,
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'status' => $data['status'],
        ]);

        return redirect()->route('company.job-categories.index')->with('sweetalert', [
            'icon' => 'success', 'title' => 'Department created', 'text' => $data['name'],
        ]);
    }

    public function edit(Request $request, JobCategory $jobCategory): View
    {
        $company = $this->company($request);
        $this->ensureOwned($company, $jobCategory);
        $this->ensureDepartmentInScope($this->assignedDepartments($request, $company), $jobCategory->id);

        return view('company.job_categories.edit', [
            'company' => $company,
            'category' => $jobCategory,
        ]);
    }

    public function update(Request $request, JobCategory $jobCategory): RedirectResponse
    {
        $company = $this->company($request);
        $this->ensureOwned($company, $jobCategory);
        $this->ensureDepartmentInScope($this->assignedDepartments($request, $company), $jobCategory->id);

        $data = $this->validated($request, $company, $jobCategory);

        $jobCategory->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'status' => $data['status'],
        ]);

        return redirect()->route('company.job-categories.index')->with('sweetalert', [
            'icon' => 'success', 'title' => 'Department updated', 'text' => $data['name'],
        ]);
    }

    public function destroy(Request $request, JobCategory $jobCategory): RedirectResponse
    {
        $company = $this->company($request);
        $this->ensureOwned($company, $jobCategory);
        $this->ensureDepartmentInScope($this->assignedDepartments($request, $company), $jobCategory->id);

        $name = $jobCategory->name;
        $jobCategory->delete();

        return back()->with('sweetalert', ['icon' => 'success', 'title' => 'Department deleted', 'text' => $name]);
    }

    private function company(Request $request): Tenant
    {
        return $request->user()->company();
    }

    private function ensureOwned(Tenant $company, JobCategory $category): void
    {
        abort_unless($category->tenant_id === $company->id, 404);
    }

    private function validated(Request $request, Tenant $company, ?JobCategory $category = null): array
    {
        return $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('job_categories', 'name')->where('tenant_id', $company->id)->ignore($category),
            ],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);
    }
}

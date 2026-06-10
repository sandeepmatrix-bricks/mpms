<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\ApplicationStatus;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Company-managed application statuses. The shared defaults (tenant_id = null)
 * are read-only here; a company can add/edit/remove its own statuses, which then
 * appear in the applicant Status dropdowns alongside the defaults.
 */
class ApplicationStatusController extends Controller
{
    public function index(Request $request): View
    {
        $company = $this->company($request);

        return view('company.statuses.index', [
            'company' => $company,
            'defaults' => ApplicationStatus::whereNull('tenant_id')->orderBy('sort_order')->get(),
            'custom' => ApplicationStatus::where('tenant_id', $company->id)->orderBy('sort_order')->get(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('company.statuses.create', [
            'company' => $this->company($request),
            'status' => new ApplicationStatus(['color' => '#0d6e6e', 'is_active' => true]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $company = $this->company($request);
        $data = $this->validated($request, $company);

        ApplicationStatus::create([
            'tenant_id' => $company->id,
            'slug' => $this->uniqueSlug($company, $data['label']),
            'label' => $data['label'],
            'color' => $data['color'],
            'sort_order' => (int) ($data['sort_order'] ?? 100),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('company.statuses.index')->with('sweetalert', [
            'icon' => 'success', 'title' => 'Status added', 'text' => $data['label'],
        ]);
    }

    public function edit(Request $request, ApplicationStatus $status): View
    {
        $this->ensureOwned($request, $status);

        return view('company.statuses.edit', [
            'company' => $this->company($request),
            'status' => $status,
        ]);
    }

    public function update(Request $request, ApplicationStatus $status): RedirectResponse
    {
        $this->ensureOwned($request, $status);
        $data = $this->validated($request, $this->company($request));

        $status->update([
            'label' => $data['label'],
            'color' => $data['color'],
            'sort_order' => (int) ($data['sort_order'] ?? $status->sort_order),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('company.statuses.index')->with('sweetalert', [
            'icon' => 'success', 'title' => 'Status updated', 'text' => $data['label'],
        ]);
    }

    public function destroy(Request $request, ApplicationStatus $status): RedirectResponse
    {
        $this->ensureOwned($request, $status);
        $label = $status->label;
        $status->delete();

        return back()->with('sweetalert', ['icon' => 'success', 'title' => 'Status removed', 'text' => $label]);
    }

    private function validated(Request $request, Tenant $company): array
    {
        return $request->validate([
            'label' => ['required', 'string', 'max:60'],
            'color' => ['required', 'string', 'regex:/^#([0-9a-fA-F]{6})$/'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    /** A slug unique within the company's statuses (and not colliding with defaults). */
    private function uniqueSlug(Tenant $company, string $label): string
    {
        $base = Str::slug($label, '_') ?: 'status';
        $slug = $base;
        $i = 2;

        while (ApplicationStatus::where('slug', $slug)
            ->where(fn ($q) => $q->whereNull('tenant_id')->orWhere('tenant_id', $company->id))
            ->exists()) {
            $slug = $base.'_'.$i++;
        }

        return $slug;
    }

    private function company(Request $request): Tenant
    {
        return $request->user()->company();
    }

    /** A company may only touch its own statuses, never the shared defaults. */
    private function ensureOwned(Request $request, ApplicationStatus $status): void
    {
        abort_unless($status->tenant_id === $this->company($request)->id, 404);
    }
}

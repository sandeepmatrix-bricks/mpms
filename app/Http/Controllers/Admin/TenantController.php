<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function index(): View
    {
        $tenants = Tenant::withCount(['pages', 'collections', 'memberships'])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.tenants.index', compact('tenants'));
    }

    public function create(): View
    {
        return view('admin.tenants.create', ['tenant' => new Tenant()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = Tenant::create($this->validated($request));

        return redirect()->route('admin.tenants.index')
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Tenant created', 'text' => $tenant->name]);
    }

    public function edit(Tenant $tenant): View
    {
        return view('admin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $tenant->update($this->validated($request, $tenant));

        return redirect()->route('admin.tenants.index')
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Tenant updated', 'text' => $tenant->name]);
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        $name = $tenant->name;
        $tenant->delete();

        return back()->with('sweetalert', ['icon' => 'success', 'title' => 'Tenant deleted', 'text' => $name]);
    }

    private function validated(Request $request, ?Tenant $tenant = null): array
    {
        $request->merge([
            'slug' => Str::slug($request->filled('slug') ? $request->input('slug') : $request->input('name')),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('tenants', 'slug')->ignore($tenant)],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'theme' => ['nullable', 'string', 'max:100'],
        ]);

        $settings = $tenant?->settings ?? [];
        $settings['branding']['theme'] = $data['theme'] ?? ($settings['branding']['theme'] ?? 'default');

        return [
            'name' => $data['name'],
            'slug' => $data['slug'],
            'status' => $data['status'],
            'settings' => $settings,
        ];
    }
}

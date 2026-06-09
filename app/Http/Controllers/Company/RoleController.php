<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Tenant;
use App\Support\Modules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Company-defined roles. Permissions are presented as a Module × Action
 * (Read/Write/Edit/Delete) grid built from the company module registry
 * (config/modules.php → 'company'); adding a company module there makes it
 * appear here automatically. Platform modules are never offered to companies.
 */
class RoleController extends Controller
{
    public function index(Request $request): View
    {
        $company = $this->company($request);

        $roles = Role::where('tenant_id', $company->id)
            ->withCount(['permissions', 'memberships'])
            ->orderBy('name')
            ->get();

        return view('company.roles.index', compact('company', 'roles'));
    }

    public function create(Request $request): View
    {
        return view('company.roles.create', $this->gridData(new Role) + [
            'company' => $this->company($request),
            'role' => new Role,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $company = $this->company($request);
        $data = $this->validated($request, $company);

        $role = Role::create(['tenant_id' => $company->id, 'name' => $data['name'], 'scope' => 'tenant']);
        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()->route('company.roles.index')->with('sweetalert', [
            'icon' => 'success', 'title' => 'Role created', 'text' => $role->name,
        ]);
    }

    public function edit(Request $request, Role $role): View
    {
        $company = $this->company($request);
        $this->ensureOwned($company, $role);

        return view('company.roles.edit', $this->gridData($role) + [
            'company' => $company,
            'role' => $role,
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $company = $this->company($request);
        $this->ensureOwned($company, $role);

        $data = $this->validated($request, $company, $role);
        $role->update(['name' => $data['name']]);
        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()->route('company.roles.index')->with('sweetalert', [
            'icon' => 'success', 'title' => 'Role updated', 'text' => $role->name,
        ]);
    }

    public function destroy(Request $request, Role $role): RedirectResponse
    {
        $company = $this->company($request);
        $this->ensureOwned($company, $role);

        if ($role->memberships()->exists()) {
            return back()->with('sweetalert', [
                'icon' => 'error', 'title' => 'Role in use', 'text' => 'Reassign its users first.',
            ]);
        }

        $name = $role->name;
        $role->delete();

        return back()->with('sweetalert', ['icon' => 'success', 'title' => 'Role deleted', 'text' => $name]);
    }

    private function company(Request $request): Tenant
    {
        return $request->user()->company();
    }

    /** Shared data for the permission grid (company modules, actions, key=>id map, selected ids). */
    private function gridData(Role $role): array
    {
        return [
            'modules' => Modules::company(),
            'actions' => Modules::actions(),
            'permMap' => Modules::ensure(), // key => id, also creates any missing rows
            'selected' => $role->exists ? $role->permissions->pluck('id')->all() : [],
        ];
    }

    private function ensureOwned(Tenant $company, Role $role): void
    {
        abort_unless($role->tenant_id === $company->id, 404);
    }

    private function validated(Request $request, Tenant $company, ?Role $role = null): array
    {
        return $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('roles', 'name')->where('tenant_id', $company->id)->ignore($role),
            ],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);
    }
}

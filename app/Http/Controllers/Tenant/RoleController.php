<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Company-side, fully-custom roles. A company defines its own roles (Manager,
 * Employee, …) and ticks which permissions each one carries. Platform-only
 * permissions (e.g. manage_tenants) are never offered here.
 */
class RoleController extends Controller
{
    private const PLATFORM_ONLY = ['manage_tenants'];

    public function index(Tenant $tenant): View
    {
        $roles = Role::where('tenant_id', $tenant->id)
            ->withCount(['permissions', 'memberships'])
            ->orderBy('name')
            ->get();

        return view('tenant.roles.index', compact('tenant', 'roles'));
    }

    public function create(Tenant $tenant): View
    {
        return view('tenant.roles.create', [
            'tenant' => $tenant,
            'role' => new Role,
            'permissions' => $this->assignablePermissions(),
            'selected' => [],
        ]);
    }

    public function store(Request $request, Tenant $tenant): RedirectResponse
    {
        $data = $this->validated($request, $tenant);

        $role = Role::create([
            'tenant_id' => $tenant->id,
            'name' => $data['name'],
            'scope' => 'tenant',
        ]);
        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()->route('tenant.roles.index', $tenant)->with('sweetalert', [
            'icon' => 'success',
            'title' => 'Role created',
            'text' => $role->name,
        ]);
    }

    public function edit(Tenant $tenant, Role $role): View
    {
        $this->ensureOwnedBy($tenant, $role);

        return view('tenant.roles.edit', [
            'tenant' => $tenant,
            'role' => $role,
            'permissions' => $this->assignablePermissions(),
            'selected' => $role->permissions->pluck('id')->all(),
        ]);
    }

    public function update(Request $request, Tenant $tenant, Role $role): RedirectResponse
    {
        $this->ensureOwnedBy($tenant, $role);

        $data = $this->validated($request, $tenant, $role);
        $role->update(['name' => $data['name']]);
        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()->route('tenant.roles.index', $tenant)->with('sweetalert', [
            'icon' => 'success',
            'title' => 'Role updated',
            'text' => $role->name,
        ]);
    }

    public function destroy(Tenant $tenant, Role $role): RedirectResponse
    {
        $this->ensureOwnedBy($tenant, $role);

        if ($role->memberships()->exists()) {
            return back()->with('sweetalert', [
                'icon' => 'error',
                'title' => 'Role in use',
                'text' => 'Reassign its users before deleting this role.',
            ]);
        }

        $name = $role->name;
        $role->delete();

        return back()->with('sweetalert', ['icon' => 'success', 'title' => 'Role deleted', 'text' => $name]);
    }

    private function assignablePermissions()
    {
        return Permission::whereNotIn('key', self::PLATFORM_ONLY)->orderBy('key')->get();
    }

    private function ensureOwnedBy(Tenant $tenant, Role $role): void
    {
        abort_unless($role->tenant_id === $tenant->id, 404);
    }

    private function validated(Request $request, Tenant $tenant, ?Role $role = null): array
    {
        return $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('roles', 'name')->where('tenant_id', $tenant->id)->ignore($role),
            ],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);
    }
}

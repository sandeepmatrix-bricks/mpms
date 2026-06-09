<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Support\Modules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Platform roles for Sub Admins. Permissions are presented as a Module × Action
 * (Read/Write/Edit/Delete) grid built from the module registry — adding a module
 * to config/modules.php makes it appear here automatically.
 */
class RoleController extends Controller
{
    public function index(): View
    {
        // Platform-level roles only; per-company roles live inside each company.
        $roles = Role::whereNull('tenant_id')
            ->withCount(['permissions', 'memberships'])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('admin.roles.create', $this->gridData(new Role) + ['role' => new Role]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $role = Role::create(['name' => $data['name'], 'scope' => 'platform', 'tenant_id' => null]);
        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()->route('admin.roles.index')
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Role created', 'text' => $role->name]);
    }

    public function edit(Role $role): View
    {
        return view('admin.roles.edit', $this->gridData($role) + compact('role'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $this->validated($request);

        $role->update(['name' => $data['name']]);
        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()->route('admin.roles.index')
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Role updated', 'text' => $role->name]);
    }

    public function destroy(Role $role): RedirectResponse
    {
        $name = $role->name;
        $role->delete();

        return back()->with('sweetalert', ['icon' => 'success', 'title' => 'Role deleted', 'text' => $name]);
    }

    /** Shared data for the permission grid (modules, actions, key=>id map, selected ids). */
    private function gridData(Role $role): array
    {
        return [
            'modules' => Modules::platform(),
            'actions' => Modules::actions(),
            'permMap' => Modules::ensure(), // key => id, also creates any missing rows
            'selected' => $role->exists ? $role->permissions->pluck('id')->all() : [],
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);
    }
}

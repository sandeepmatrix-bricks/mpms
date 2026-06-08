<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::withCount(['permissions', 'memberships'])->orderBy('name')->paginate(15);

        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('admin.roles.create', [
            'role' => new Role(),
            'permissions' => Permission::orderBy('key')->get(),
            'selected' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $role = Role::create($this->validated($request));
        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('admin.roles.index')
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Role created', 'text' => $role->name]);
    }

    public function edit(Role $role): View
    {
        return view('admin.roles.edit', [
            'role' => $role,
            'permissions' => Permission::orderBy('key')->get(),
            'selected' => $role->permissions->pluck('id')->all(),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $role->update($this->validated($request));
        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('admin.roles.index')
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Role updated', 'text' => $role->name]);
    }

    public function destroy(Role $role): RedirectResponse
    {
        $name = $role->name;
        $role->delete();

        return back()->with('sweetalert', ['icon' => 'success', 'title' => 'Role deleted', 'text' => $name]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'scope' => ['required', Rule::in(['platform', 'tenant'])],
        ]);
    }
}

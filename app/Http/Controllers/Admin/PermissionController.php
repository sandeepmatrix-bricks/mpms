<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function index(): View
    {
        $permissions = Permission::withCount('roles')->orderBy('key')->paginate(20);

        return view('admin.permissions.index', compact('permissions'));
    }

    public function create(): View
    {
        return view('admin.permissions.create', ['permission' => new Permission()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $permission = Permission::create($this->validated($request));

        return redirect()->route('admin.permissions.index')
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Permission created', 'text' => $permission->key]);
    }

    public function edit(Permission $permission): View
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $permission->update($this->validated($request, $permission));

        return redirect()->route('admin.permissions.index')
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Permission updated', 'text' => $permission->key]);
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $key = $permission->key;
        $permission->delete();

        return back()->with('sweetalert', ['icon' => 'success', 'title' => 'Permission deleted', 'text' => $key]);
    }

    private function validated(Request $request, ?Permission $permission = null): array
    {
        $request->merge([
            'key' => Str::of($request->input('key'))->lower()->snake()->toString(),
        ]);

        return $request->validate([
            'key' => ['required', 'string', 'max:255', Rule::unique('permissions', 'key')->ignore($permission)],
        ]);
    }
}

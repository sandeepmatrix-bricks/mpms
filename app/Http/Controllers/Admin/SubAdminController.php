<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Sub Admins — the Super Admin's internal team (Platform Manager, Support Admin,
 * Billing Admin, …). They sign in at /admin/login like the Super Admin, but are
 * is_admin WITHOUT is_super, so their access is limited by the platform role
 * they're given (a role with tenant_id = null).
 */
class SubAdminController extends Controller
{
    public function index(): View
    {
        $subAdmins = User::where('is_admin', true)
            ->where('is_super', false)
            ->with(['memberships' => fn ($q) => $q->whereNull('tenant_id')->with('role')])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.sub-admins.index', compact('subAdmins'));
    }

    public function create(): View
    {
        return view('admin.sub-admins.create', [
            'user' => new User,
            'roles' => $this->platformRoles(),
            'membership' => new Membership,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role_id' => ['required', $this->platformRoleRule()],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'status' => 'active',
            'is_admin' => true,
            'is_super' => false,
        ]);

        Membership::create([
            'user_id' => $user->id,
            'tenant_id' => null, // platform-level membership
            'role_id' => $data['role_id'],
        ]);

        return redirect()->route('admin.sub-admins.index')->with('sweetalert', [
            'icon' => 'success', 'title' => 'Sub Admin created', 'text' => $user->email,
        ]);
    }

    public function edit(User $subAdmin): View
    {
        abort_unless($subAdmin->is_admin && ! $subAdmin->is_super, 404);

        return view('admin.sub-admins.edit', [
            'user' => $subAdmin,
            'roles' => $this->platformRoles(),
            'membership' => $subAdmin->memberships()->whereNull('tenant_id')->first(),
        ]);
    }

    public function update(Request $request, User $subAdmin): RedirectResponse
    {
        abort_unless($subAdmin->is_admin && ! $subAdmin->is_super, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($subAdmin)],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'role_id' => ['required', $this->platformRoleRule()],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $subAdmin->update(array_filter([
            'name' => $data['name'],
            'email' => $data['email'],
            'status' => $data['status'],
            'password' => $data['password'] ?? null,
        ], fn ($v) => $v !== null));

        $membership = $subAdmin->memberships()->whereNull('tenant_id')->first();
        $membership
            ? $membership->update(['role_id' => $data['role_id']])
            : $subAdmin->memberships()->create(['tenant_id' => null, 'role_id' => $data['role_id']]);

        return redirect()->route('admin.sub-admins.index')->with('sweetalert', [
            'icon' => 'success', 'title' => 'Sub Admin updated', 'text' => $subAdmin->email,
        ]);
    }

    public function destroy(User $subAdmin): RedirectResponse
    {
        abort_unless($subAdmin->is_admin && ! $subAdmin->is_super, 404);

        $email = $subAdmin->email;
        $subAdmin->delete();

        return back()->with('sweetalert', ['icon' => 'success', 'title' => 'Sub Admin removed', 'text' => $email]);
    }

    /** Platform roles = roles not tied to any company. */
    private function platformRoles()
    {
        return Role::whereNull('tenant_id')->orderBy('name')->get();
    }

    private function platformRoleRule()
    {
        return Rule::exists('roles', 'id')->whereNull('tenant_id');
    }
}

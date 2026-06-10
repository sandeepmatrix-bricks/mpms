<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Services\CredentialProvisioner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\View\View;

/**
 * Company-side team management. A Company Admin (anyone with `manage_users`)
 * creates users for their own company; credentials are auto-generated and
 * emailed, and each user is given one of the company's roles.
 */
class UserController extends Controller
{
    public function __construct(private readonly CredentialProvisioner $provisioner) {}

    public function index(Tenant $tenant): View
    {
        $memberships = $tenant->memberships()
            ->with(['user', 'role'])
            ->get()
            ->sortBy(fn (Membership $m) => $m->user?->name);

        return view('tenant.users.index', compact('tenant', 'memberships'));
    }

    public function create(Tenant $tenant): View
    {
        return view('tenant.users.create', [
            'tenant' => $tenant,
            'roles' => $this->roles($tenant),
            'membership' => new Membership,
        ]);
    }

    public function store(Request $request, Tenant $tenant): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role_id' => ['required', $this->roleBelongsToTenant($tenant)],
        ]);

        $role = Role::findOrFail($data['role_id']);
        $this->provisioner->provisionMember($tenant, $role, $data['name'], $data['email']);

        return redirect()->route('tenant.users.index', $tenant)->with('sweetalert', [
            'icon' => 'success',
            'title' => 'User created',
            'text' => "Login details were emailed to {$data['email']}.",
        ]);
    }

    public function edit(Tenant $tenant, User $user): View
    {
        $membership = $this->membershipFor($tenant, $user);

        return view('tenant.users.edit', [
            'tenant' => $tenant,
            'user' => $user,
            'membership' => $membership,
            'roles' => $this->roles($tenant),
        ]);
    }

    public function update(Request $request, Tenant $tenant, User $user): RedirectResponse
    {
        $membership = $this->membershipFor($tenant, $user);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'role_id' => ['required', $this->roleBelongsToTenant($tenant)],
        ]);

        $user->update(['name' => $data['name'], 'email' => $data['email'], 'status' => $data['status']]);
        $membership->update(['role_id' => $data['role_id']]);

        return redirect()->route('tenant.users.index', $tenant)->with('sweetalert', [
            'icon' => 'success',
            'title' => 'User updated',
            'text' => $user->email,
        ]);
    }

    public function destroy(Tenant $tenant, User $user): RedirectResponse
    {
        $membership = $this->membershipFor($tenant, $user);
        $membership->delete();

        // Remove the account entirely if it no longer belongs to any company.
        if (! $user->is_admin && $user->memberships()->doesntExist()) {
            $user->delete();
        }

        return back()->with('sweetalert', [
            'icon' => 'success',
            'title' => 'User removed',
            'text' => $user->email,
        ]);
    }

    /** This company's own roles (the ones that can be assigned to its users). */
    private function roles(Tenant $tenant)
    {
        return Role::where('tenant_id', $tenant->id)->orderBy('name')->get();
    }

    private function roleBelongsToTenant(Tenant $tenant): Exists
    {
        return Rule::exists('roles', 'id')->where('tenant_id', $tenant->id);
    }

    private function membershipFor(Tenant $tenant, User $user): Membership
    {
        return Membership::where('tenant_id', $tenant->id)
            ->where('user_id', $user->id)
            ->firstOrFail();
    }
}

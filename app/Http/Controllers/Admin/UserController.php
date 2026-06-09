<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CredentialsMail;
use App\Models\Membership;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Services\CredentialProvisioner;
use App\Support\Modules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Super Admin → Users.
 *
 * Create a login for a company: pick the company from a dropdown of ACTIVE
 * companies, set name / email / phone / password. The user is linked to that
 * company (with its Admin role) and can then sign in at /company/login to work
 * inside that company's portal.
 */
class UserController extends Controller
{
    public function __construct(private readonly CredentialProvisioner $provisioner) {}

    /**
     * Permission keys granted to a company's Admin role: read/write/edit/delete
     * on every company module (config/modules.php → 'company'). Generated so new
     * company modules are automatically covered.
     *
     * @return array<int,string>
     */
    private function adminPermissionKeys(): array
    {
        $keys = [];
        foreach (array_keys(Modules::company()) as $module) {
            foreach (['read', 'write', 'edit', 'delete'] as $action) {
                $keys[] = Modules::key($module, $action);
            }
        }

        return $keys;
    }

    public function index(): View
    {
        $users = User::where('is_admin', false)
            ->with(['memberships' => fn ($q) => $q->whereNotNull('tenant_id')->with(['tenant', 'role'])])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'user' => new User,
            'companies' => $this->activeCompanies(),
            'membership' => new Membership,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'auto_password' => ['nullable', 'boolean'],
            'password' => [$request->boolean('auto_password') ? 'nullable' : 'required', 'string', 'min:8'],
            'tenant_id' => ['required', Rule::exists('tenants', 'id')->where('status', 'active')],
        ]);

        $company = Tenant::findOrFail($data['tenant_id']);

        // Auto-generate or use the typed password.
        $password = $request->boolean('auto_password')
            ? $this->provisioner->generatePassword()
            : $data['password'];

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $password,
            'status' => 'active',
            'is_admin' => false,
            'is_super' => false,
            'must_change_password' => false,
        ]);

        Membership::create([
            'user_id' => $user->id,
            'tenant_id' => $company->id,
            'role_id' => $this->companyAdminRole($company)->id,
        ]);

        // Email the login URL + credentials to the new user.
        $mailed = $this->emailCredentials($user, $password, $company);

        return redirect()->route('admin.users.index')->with('sweetalert', [
            'icon' => $mailed ? 'success' : 'warning',
            'title' => 'User created',
            'text' => $mailed
                ? "Login details emailed to {$user->email}."
                : "User created, but the email could not be sent. Password: {$password}",
        ]);
    }

    public function edit(User $user): View
    {
        abort_if($user->is_admin, 404); // company users only (sub admins live elsewhere)

        return view('admin.users.edit', [
            'user' => $user,
            'companies' => $this->activeCompanies(),
            'membership' => $user->memberships()->whereNotNull('tenant_id')->first(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_if($user->is_admin, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'password' => ['nullable', 'string', 'min:8'],
            'tenant_id' => ['required', 'exists:tenants,id'],
        ]);

        $user->update(array_filter([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'status' => $data['status'],
            'password' => $data['password'] ?? null,
        ], fn ($v) => $v !== null));

        $company = Tenant::findOrFail($data['tenant_id']);
        $membership = $user->memberships()->whereNotNull('tenant_id')->first();
        $attributes = ['tenant_id' => $company->id, 'role_id' => $this->companyAdminRole($company)->id];

        $membership ? $membership->update($attributes) : $user->memberships()->create($attributes);

        return redirect()->route('admin.users.index')->with('sweetalert', [
            'icon' => 'success', 'title' => 'User updated', 'text' => $user->email,
        ]);
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->is_admin, 404);

        $email = $user->email;
        $user->delete();

        return back()->with('sweetalert', ['icon' => 'success', 'title' => 'User removed', 'text' => $email]);
    }

    /** Send the welcome email; never let a mail failure abort user creation. */
    private function emailCredentials(User $user, string $password, Tenant $company): bool
    {
        try {
            Mail::to($user->email)->send(new CredentialsMail($user, $password, $company));

            return true;
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }

    private function activeCompanies()
    {
        return Tenant::where('status', 'active')->orderBy('name')->get();
    }

    /** Get (or create) the company's Admin role so the user has access on first login. */
    private function companyAdminRole(Tenant $company): Role
    {
        $role = Role::where('tenant_id', $company->id)->where('name', 'Admin')->first();

        if (! $role) {
            $role = Role::create(['tenant_id' => $company->id, 'name' => 'Admin', 'scope' => 'tenant']);
            $role->permissions()->sync(
                collect($this->adminPermissionKeys())->map(fn ($k) => Permission::firstOrCreate(['key' => $k])->id)->all()
            );
        }

        return $role;
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Services\CredentialProvisioner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Super Admin company management.
 *
 * Registration is a two-step wizard:
 *   Step 1  create() / store()        -> basic company details only
 *   Step 2  setupAdmin() / storeAdmin -> the company admin account
 *   then    credentials()             -> one-time confirmation screen
 */
class CompanyController extends Controller
{
    /** Permissions the bootstrapped company-admin role receives. */
    private const TENANT_PERMISSIONS = [
        'view_dashboard',
        'manage_users',
        'manage_roles',
        'manage_settings',
    ];

    public function __construct(private readonly CredentialProvisioner $provisioner) {}

    public function index(): View
    {
        $companies = Tenant::withCount('memberships')
            ->with('ownerMembership.user')
            ->orderBy('name')->paginate(15);

        return view('admin.companies.index', compact('companies'));
    }

    /* ----- Step 1: basic company details ----- */

    public function create(): View
    {
        return view('admin.companies.create', ['company' => new Tenant]);
    }

    public function store(Request $request): RedirectResponse
    {
        $company = Tenant::create($this->validatedCompany($request));

        return redirect()->route('admin.companies.index')->with('sweetalert', [
            'icon' => 'success',
            'title' => 'Company registered',
            'text' => "{$company->name} added. Now create its login under Users.",
        ]);
    }

    /* ----- Step 2: company admin account ----- */

    public function setupAdmin(Tenant $company): View|RedirectResponse
    {
        if ($company->memberships()->exists()) {
            return redirect()->route('admin.companies.index')->with('sweetalert', [
                'icon' => 'info', 'title' => 'Already set up', 'text' => "{$company->name} already has an admin.",
            ]);
        }

        return view('admin.companies.setup-admin', compact('company'));
    }

    public function storeAdmin(Request $request, Tenant $company): RedirectResponse
    {
        abort_if($company->memberships()->exists(), 409);

        $data = $request->validate([
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'auto_password' => ['nullable', 'boolean'],
            'password' => [$request->boolean('auto_password') ? 'nullable' : 'required', 'string', 'min:8'],
        ]);

        $result = DB::transaction(function () use ($company, $data, $request) {
            $role = $this->bootstrapAdminRole($company);

            return $this->provisioner->provisionMember(
                $company,
                $role,
                $data['admin_name'],
                $data['admin_email'],
                $request->boolean('auto_password') ? null : $data['password'],
            );
        });

        // Pass the credentials once, via flash, to the confirmation screen.
        return redirect()->route('admin.companies.credentials', $company)->with('credentials', [
            'name' => $result['user']->name,
            'email' => $result['user']->email,
            'password' => $result['password'],
        ]);
    }

    public function credentials(Tenant $company): View|RedirectResponse
    {
        $credentials = session('credentials');

        if (! $credentials) {
            return redirect()->route('admin.companies.index');
        }

        return view('admin.companies.credentials', compact('company', 'credentials'));
    }

    /* ----- Edit / status / delete ----- */

    public function edit(Tenant $company): View
    {
        return view('admin.companies.edit', compact('company'));
    }

    public function update(Request $request, Tenant $company): RedirectResponse
    {
        $company->update($this->validatedCompany($request, $company));

        return redirect()->route('admin.companies.index')
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Company updated', 'text' => $company->name]);
    }

    public function toggleStatus(Tenant $company): RedirectResponse
    {
        $company->update(['status' => $company->status === 'active' ? 'inactive' : 'active']);

        return back()->with('sweetalert', [
            'icon' => 'success',
            'title' => $company->status === 'active' ? 'Company activated' : 'Company deactivated',
            'text' => $company->name,
        ]);
    }

    /** Show the reset-password page (type a new password directly — no email). */
    public function resetPasswordForm(Tenant $company): View|RedirectResponse
    {
        $admin = $company->memberships()->with('user')->oldest()->first()?->user;

        if (! $admin) {
            return redirect()->route('admin.companies.index')->with('sweetalert', [
                'icon' => 'error', 'title' => 'No admin found', 'text' => 'This company has no admin to reset.',
            ]);
        }

        // Pre-fill with a suggestion the super admin can keep or overwrite.
        $suggestion = $this->provisioner->generatePassword();

        return view('admin.companies.reset-password', compact('company', 'admin', 'suggestion'));
    }

    /** Set the new password straight away — no email, log in with it immediately. */
    public function resetPassword(Request $request, Tenant $company): RedirectResponse
    {
        $admin = $company->memberships()->with('user')->oldest()->first()?->user;
        abort_if(! $admin, 404);

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8'],
        ]);

        $admin->update([
            'password' => $data['password'],
            'must_change_password' => false,
        ]);

        return redirect()->route('admin.companies.index')->with('sweetalert', [
            'icon' => 'success',
            'title' => 'Password updated',
            'text' => "{$admin->email} can now log in with: {$data['password']}",
        ]);
    }

    /**
     * Hard delete the company and everything it owns. FKs cascade memberships,
     * roles, pages, collections and records; we then remove company-only users.
     */
    public function destroy(Tenant $company): RedirectResponse
    {
        $name = $company->name;

        DB::transaction(function () use ($company) {
            $userIds = $company->memberships()->pluck('user_id');

            $company->delete(); // cascades memberships, roles, pages, collections, records

            User::whereIn('id', $userIds)
                ->where('is_admin', false)
                ->whereDoesntHave('memberships')
                ->delete();
        });

        return back()->with('sweetalert', ['icon' => 'success', 'title' => 'Company deleted', 'text' => $name]);
    }

    /* ----- helpers ----- */

    private function bootstrapAdminRole(Tenant $company): Role
    {
        $permissionIds = collect(self::TENANT_PERMISSIONS)
            ->map(fn (string $key) => Permission::firstOrCreate(['key' => $key])->id)
            ->all();

        $role = Role::create(['tenant_id' => $company->id, 'name' => 'Admin', 'scope' => 'tenant']);
        $role->permissions()->sync($permissionIds);

        return $role;
    }

    private function validatedCompany(Request $request, ?Tenant $company = null): array
    {
        $request->merge([
            'slug' => Str::slug($request->filled('slug') ? $request->input('slug') : $request->input('name')),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('tenants', 'slug')->ignore($company)],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'gst_number' => ['nullable', 'string', 'max:50'],
            'plan' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ]);

        $data['status'] ??= 'active';

        return $data;
    }
}

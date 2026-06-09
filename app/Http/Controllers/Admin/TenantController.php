<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Services\CredentialProvisioner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TenantController extends Controller
{
    /**
     * Permission keys every company starts with. The bootstrapped company-admin
     * role is granted all of these; companies then build further custom roles
     * (Manager, Employee, …) on top of the same permission catalogue.
     */
    private const TENANT_PERMISSIONS = [
        'view_dashboard',
        'manage_users',
        'manage_roles',
        'manage_pages',
        'manage_collections',
    ];

    public function __construct(private readonly CredentialProvisioner $provisioner) {}

    public function index(): View
    {
        $tenants = Tenant::withCount(['pages', 'collections', 'memberships'])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.tenants.index', compact('tenants'));
    }

    public function create(): View
    {
        return view('admin.tenants.create', ['tenant' => new Tenant]);
    }

    /**
     * Create a company AND its first admin in one step: the tenant, a tenant-scoped
     * "Admin" role with every default permission, and a company-admin user whose
     * generated credentials are emailed to them.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, withAdmin: true);

        $tenant = DB::transaction(function () use ($data) {
            $tenant = Tenant::create($data['tenant']);

            $adminRole = $this->bootstrapAdminRole($tenant);

            $this->provisioner->provisionMember(
                $tenant,
                $adminRole,
                $data['admin']['name'],
                $data['admin']['email'],
            );

            return $tenant;
        });

        return redirect()->route('admin.tenants.index')->with('sweetalert', [
            'icon' => 'success',
            'title' => 'Company created',
            'text' => "{$tenant->name} is ready. Login details were emailed to {$data['admin']['email']}.",
        ]);
    }

    public function edit(Tenant $tenant): View
    {
        return view('admin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $data = $this->validated($request, $tenant);
        $tenant->update($data['tenant']);

        return redirect()->route('admin.tenants.index')
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Company updated', 'text' => $tenant->name]);
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        $name = $tenant->name;
        $tenant->delete();

        return back()->with('sweetalert', ['icon' => 'success', 'title' => 'Company deleted', 'text' => $name]);
    }

    /** Flip a company between active and inactive (inactive blocks member login). */
    public function toggleStatus(Tenant $tenant): RedirectResponse
    {
        $tenant->update(['status' => $tenant->status === 'active' ? 'inactive' : 'active']);

        return back()->with('sweetalert', [
            'icon' => 'success',
            'title' => $tenant->status === 'active' ? 'Company activated' : 'Company deactivated',
            'text' => $tenant->name,
        ]);
    }

    /** Generate a fresh password for the company's admin and email it. */
    public function resetPassword(Tenant $tenant): RedirectResponse
    {
        $admin = $tenant->memberships()->with('user')->oldest()->first()?->user;

        if (! $admin) {
            return back()->with('sweetalert', [
                'icon' => 'error',
                'title' => 'No admin found',
                'text' => 'This company has no user to reset.',
            ]);
        }

        $this->provisioner->resetPassword($admin, $tenant);

        return back()->with('sweetalert', [
            'icon' => 'success',
            'title' => 'Password reset',
            'text' => "New login details were emailed to {$admin->email}.",
        ]);
    }

    /** Create the per-company Admin role and grant it the default permissions. */
    private function bootstrapAdminRole(Tenant $tenant): Role
    {
        $permissionIds = collect(self::TENANT_PERMISSIONS)
            ->map(fn (string $key) => Permission::firstOrCreate(['key' => $key])->id)
            ->all();

        $role = Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin',
            'scope' => 'tenant',
        ]);

        $role->permissions()->sync($permissionIds);

        return $role;
    }

    /**
     * @return array{tenant: array<string,mixed>, admin: array<string,string>}
     */
    private function validated(Request $request, ?Tenant $tenant = null, bool $withAdmin = false): array
    {
        $request->merge([
            'slug' => Str::slug($request->filled('slug') ? $request->input('slug') : $request->input('name')),
        ]);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('tenants', 'slug')->ignore($tenant)],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'theme' => ['nullable', 'string', 'max:100'],
        ];

        if ($withAdmin) {
            $rules['admin_name'] = ['required', 'string', 'max:255'];
            $rules['admin_email'] = ['required', 'email', 'max:255', 'unique:users,email'];
        }

        $data = $request->validate($rules);

        $settings = $tenant?->settings ?? [];
        $settings['branding']['theme'] = $data['theme'] ?? ($settings['branding']['theme'] ?? 'default');

        return [
            'tenant' => [
                'name' => $data['name'],
                'slug' => $data['slug'],
                'status' => $data['status'],
                'settings' => $settings,
            ],
            'admin' => $withAdmin
                ? ['name' => $data['admin_name'], 'email' => $data['admin_email']]
                : [],
        ];
    }
}

<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\JobCategory;
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
 * Company team management. The company comes from the logged-in user (one
 * company per user); there is no company id in the URL.
 */
class UserController extends Controller
{
    public function __construct(private readonly CredentialProvisioner $provisioner) {}

    public function index(Request $request): View
    {
        $company = $this->company($request);

        $currentUserId = $request->user()->id;

        $members = $company->memberships()
            ->with(['user', 'role'])
            ->get()
            // Current user first, then the rest alphabetically.
            ->sortBy(fn (Membership $m) => ($m->user_id === $currentUserId ? '0' : '1').($m->user?->name ?? ''))
            ->values();

        return view('company.users.index', compact('company', 'members', 'currentUserId'));
    }

    public function create(Request $request): View
    {
        $company = $this->company($request);

        return view('company.users.create', [
            'company' => $company,
            'roles' => $this->roles($company),
            'jobCategories' => $this->jobCategories($company),
            'user' => new User,
            'membership' => new Membership,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $company = $this->company($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role_id' => ['required', $this->roleRule($company)],
            'job_categories' => ['nullable', 'array'],
            'job_categories.*' => [$this->jobCategoryRule($company)],
            'auto_password' => ['nullable', 'boolean'],
            'password' => [$request->boolean('auto_password') ? 'nullable' : 'required', 'string', 'min:8'],
        ]);

        $role = Role::findOrFail($data['role_id']);

        $result = $this->provisioner->provisionMember(
            $company,
            $role,
            $data['name'],
            $data['email'],
            $request->boolean('auto_password') ? null : $data['password'],
            jobCategoryIds: $data['job_categories'] ?? [],
        );

        if (! empty($data['phone'])) {
            $result['user']->update(['phone' => $data['phone']]);
        }

        return redirect()->route('company.users.index')->with('sweetalert', [
            'icon' => 'success',
            'title' => 'User created',
            'text' => "Login details were emailed to {$data['email']}.",
        ]);
    }

    public function edit(Request $request, User $user): View
    {
        $company = $this->company($request);
        $membership = $this->membershipFor($company, $user);

        return view('company.users.edit', [
            'company' => $company,
            'user' => $user,
            'membership' => $membership,
            'roles' => $this->roles($company),
            'jobCategories' => $this->jobCategories($company),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $company = $this->company($request);
        $membership = $this->membershipFor($company, $user);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'role_id' => ['required', $this->roleRule($company)],
            'job_categories' => ['nullable', 'array'],
            'job_categories.*' => [$this->jobCategoryRule($company)],
            'password' => ['nullable', 'string', 'min:8'],
            'force_change' => ['nullable', 'boolean'],
        ]);

        $attributes = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'status' => $data['status'],
        ];

        // Optional password reset (e.g. the user forgot theirs).
        $passwordReset = filled($data['password'] ?? null);
        if ($passwordReset) {
            $attributes['password'] = $data['password'];
            $attributes['must_change_password'] = $request->boolean('force_change');
        }

        $user->update($attributes);
        $membership->update([
            'role_id' => $data['role_id'],
            'job_category_ids' => $data['job_categories'] ?? [],
        ]);

        return redirect()->route('company.users.index')->with('sweetalert', [
            'icon' => 'success',
            'title' => 'User updated',
            'text' => $passwordReset ? "Password reset for {$user->email}" : $user->email,
        ]);
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $company = $this->company($request);
        $membership = $this->membershipFor($company, $user);

        $membership->delete();

        if (! $user->is_admin && $user->memberships()->doesntExist()) {
            $user->delete();
        }

        return back()->with('sweetalert', ['icon' => 'success', 'title' => 'User removed', 'text' => $user->email]);
    }

    private function company(Request $request): Tenant
    {
        return $request->user()->company();
    }

    private function roles(Tenant $company)
    {
        return Role::where('tenant_id', $company->id)->orderBy('name')->get();
    }

    private function jobCategories(Tenant $company)
    {
        return JobCategory::where('tenant_id', $company->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    private function roleRule(Tenant $company): Exists
    {
        return Rule::exists('roles', 'id')->where('tenant_id', $company->id);
    }

    private function jobCategoryRule(Tenant $company): Exists
    {
        return Rule::exists('job_categories', 'id')->where('tenant_id', $company->id);
    }

    private function membershipFor(Tenant $company, User $user): Membership
    {
        return Membership::where('tenant_id', $company->id)
            ->where('user_id', $user->id)
            ->firstOrFail();
    }
}

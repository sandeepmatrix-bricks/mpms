<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MembershipController extends Controller
{
    public function index(): View
    {
        $memberships = Membership::with(['user', 'tenant', 'role'])
            ->latest()
            ->paginate(15);

        return view('admin.memberships.index', compact('memberships'));
    }

    public function create(): View
    {
        return view('admin.memberships.create', $this->formData() + ['membership' => new Membership()]);
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            Membership::create($this->validated($request));
        } catch (UniqueConstraintViolationException) {
            return back()->withInput()->with('sweetalert', [
                'icon' => 'error',
                'title' => 'Duplicate membership',
                'text' => 'That user already has this role in that scope.',
            ]);
        }

        return redirect()->route('admin.memberships.index')
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Membership created', 'text' => 'Assignment saved.']);
    }

    public function edit(Membership $membership): View
    {
        return view('admin.memberships.edit', $this->formData() + compact('membership'));
    }

    public function update(Request $request, Membership $membership): RedirectResponse
    {
        try {
            $membership->update($this->validated($request));
        } catch (UniqueConstraintViolationException) {
            return back()->withInput()->with('sweetalert', [
                'icon' => 'error',
                'title' => 'Duplicate membership',
                'text' => 'That user already has this role in that scope.',
            ]);
        }

        return redirect()->route('admin.memberships.index')
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Membership updated', 'text' => 'Assignment saved.']);
    }

    public function destroy(Membership $membership): RedirectResponse
    {
        $membership->delete();

        return back()->with('sweetalert', ['icon' => 'success', 'title' => 'Membership removed', 'text' => 'Assignment deleted.']);
    }

    private function formData(): array
    {
        return [
            'users' => User::orderBy('name')->get(),
            'tenants' => Tenant::orderBy('name')->get(),
            'roles' => Role::orderBy('name')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'tenant_id' => ['nullable', 'exists:tenants,id'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $data['tenant_id'] = $data['tenant_id'] ?: null; // empty select => platform membership

        return $data;
    }
}

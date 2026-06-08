<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::withCount('memberships')->orderBy('name')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create', ['user' => new User()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = User::create($this->validated($request));

        return redirect()->route('admin.users.index')
            ->with('sweetalert', ['icon' => 'success', 'title' => 'User created', 'text' => $user->email]);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $user->update($this->validated($request, $user));

        return redirect()->route('admin.users.index')
            ->with('sweetalert', ['icon' => 'success', 'title' => 'User updated', 'text' => $user->email]);
    }

    public function destroy(User $user): RedirectResponse
    {
        $email = $user->email;
        $user->delete();

        return back()->with('sweetalert', ['icon' => 'success', 'title' => 'User deleted', 'text' => $email]);
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'is_admin' => ['nullable', 'boolean'],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8'],
        ]);

        $data['is_admin'] = $request->boolean('is_admin');

        if (blank($data['password'])) {
            unset($data['password']); // keep the existing password on edit
        }

        return $data;
    }
}

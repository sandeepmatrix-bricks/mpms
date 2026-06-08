<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check() && ($landing = $this->landingRoute(Auth::user()))) {
            return redirect($landing);
        }

        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $landing = $this->landingRoute(Auth::user());

        if ($landing === null) {
            Auth::logout();

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'This account has no portal access.']);
        }

        $request->session()->regenerate();

        return redirect()->intended($landing);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * Where a user lands after login:
     *  - platform admins (is_admin) -> the platform console
     *  - tenant members -> their company portal
     *  - otherwise null (no access)
     */
    private function landingRoute(User $user): ?string
    {
        if ($user->is_admin) {
            return route('admin.dashboard');
        }

        $membership = $user->memberships()
            ->whereNotNull('tenant_id')
            ->with('tenant')
            ->first();

        if ($membership && $membership->tenant) {
            return route('tenant.dashboard', $membership->tenant);
        }

        return null;
    }
}

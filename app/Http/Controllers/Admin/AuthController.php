<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesPasswordResets;
use App\Http\Controllers\Controller;
use App\Support\ResolvesLanding;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    use HandlesPasswordResets, ResolvesLanding;

    protected function passwordResetConfig(): array
    {
        return ['views' => 'admin.auth', 'reset' => 'admin.password.reset', 'login' => 'admin.login'];
    }

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

        // The platform console is for Super Admins only. Company users have their
        // own login at /company/login.
        if (! Auth::user()->is_admin) {
            Auth::logout();

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Please use the company login at /company/login.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}

<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Concerns\HandlesPasswordResets;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Company login — completely separate from the Super Admin console.
 * Only users who belong to a company (and are not platform admins) may sign in.
 */
class AuthController extends Controller
{
    use HandlesPasswordResets;

    protected function passwordResetConfig(): array
    {
        return ['views' => 'company.auth', 'reset' => 'company.password.reset', 'login' => 'company.login'];
    }

    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->company()) {
            return redirect()->route('company.dashboard');
        }

        return view('company.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $user = Auth::user();

        if ($user->is_admin || ! $user->company()) {
            Auth::logout();

            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'This login is for company accounts only.']);
        }

        $request->session()->regenerate();

        ActivityLog::record('login', "{$user->name} logged in", ['user' => $user]);

        return redirect()->intended(route('company.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('company.login');
    }
}

<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Self-service "forgot password" flow, shared by the Admin and Company logins.
 * The using controller supplies the area's view folder and route names via
 * passwordResetConfig() so each area keeps its own branded pages and the email
 * link lands on the correct reset screen.
 */
trait HandlesPasswordResets
{
    /**
     * @return array{views: string, reset: string, login: string}
     *   e.g. ['views' => 'admin.auth', 'reset' => 'admin.password.reset', 'login' => 'admin.login']
     */
    abstract protected function passwordResetConfig(): array;

    /** Step 1 — the "enter your email" form. */
    public function showForgot(): View
    {
        return view($this->passwordResetConfig()['views'].'.forgot-password');
    }

    /** Step 2 — email the reset link (pointing at this area's reset page). */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $cfg = $this->passwordResetConfig();

        ResetPassword::createUrlUsing(fn ($user, string $token) => route($cfg['reset'], [
            'token' => $token,
            'email' => $request->email,
        ]));

        $status = Password::sendResetLink($request->only('email'));

        return back()->with('status', __($status))->withInput($request->only('email'));
    }

    /** Step 3 — the "set a new password" form opened from the email link. */
    public function showReset(Request $request, string $token): View
    {
        return view($this->passwordResetConfig()['views'].'.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /** Step 4 — persist the new password and send them back to login. */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                    'must_change_password' => false,
                ])->save();

                event(new PasswordReset($user));
            }
        );

        $cfg = $this->passwordResetConfig();

        return $status === Password::PASSWORD_RESET
            ? redirect()->route($cfg['login'])->with('status', __($status))
            : back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
    }
}

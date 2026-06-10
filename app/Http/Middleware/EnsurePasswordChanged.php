<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Forces auto-provisioned users to set their own password on first login.
 *
 * While `must_change_password` is true, every request is redirected to the
 * change-password screen (except that screen itself and logout), so the user
 * cannot reach any portal until they've chosen a new password.
 */
class EnsurePasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password
            && ! $request->routeIs('password.change', 'password.change.update', 'admin.logout', 'company.logout')) {
            return redirect()->route('password.change');
        }

        return $next($request);
    }
}

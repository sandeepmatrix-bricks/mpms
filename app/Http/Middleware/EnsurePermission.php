<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Route guard for a single permission key, e.g. `permission:manage_users`.
 *
 * Runs after the tenant context is set, so the check is scoped to the area being
 * viewed. The top platform owner (is_super) always passes. When denied, the user
 * is sent back with a friendly toast instead of a raw 403 page.
 */
class EnsurePermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if ($request->user()?->hasPermission($permission)) {
            return $next($request);
        }

        $notice = [
            'icon' => 'error',
            'title' => 'Access denied',
            'text' => 'You do not have permission to do that.',
        ];

        // Land somewhere safe (the area dashboard), avoiding a redirect loop back
        // onto the forbidden page or a bounce to the login screen.
        $fallback = $request->is('company*') ? route('company.dashboard') : route('admin.dashboard');
        $previous = url()->previous();
        $deadEnds = [url()->current(), url('/'), url('/').'/'];
        $target = ($previous && ! in_array(rtrim($previous, '/'), array_map(fn ($u) => rtrim($u, '/'), $deadEnds), true))
            ? $previous
            : $fallback;

        return redirect($target)->with('sweetalert', $notice);
    }
}

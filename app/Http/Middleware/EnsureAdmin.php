<?php

namespace App\Http\Middleware;

use App\Services\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates the platform admin panel.
 *
 * Auth is the simple `is_admin` flag (the platform/parent admin, e.g. Priya).
 * Once past the gate the request runs in PLATFORM mode, so the BelongsToTenant
 * global scope is bypassed and the panel sees every tenant's data.
 */
class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('admin.login');
        }

        if (! $user->is_admin) {
            abort(403, 'Platform administrators only.');
        }

        app(TenantContext::class)->setTenant(null, true);

        return $next($request);
    }
}

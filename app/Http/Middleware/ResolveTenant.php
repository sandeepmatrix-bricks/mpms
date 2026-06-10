<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Unauthenticated.');
        }

        $tenantContext = app(TenantContext::class);
        $tenantRoute = $request->route('tenant');
        $tenant = null;

        if ($tenantRoute !== null) {
            $tenant = $tenantRoute instanceof Tenant
                ? $tenantRoute
                : Tenant::where('slug', $tenantRoute)->firstOrFail();
        }

        $platformMembership = $user->memberships()->whereNull('tenant_id')->first();
        $tenantMembership = $tenant
            ? $user->memberships()->where('tenant_id', $tenant->id)->first()
            : null;

        if ($tenant !== null) {
            if (! $tenantMembership && ! $platformMembership) {
                abort(403, 'Unauthorized tenant access.');
            }

            // A deactivated company is closed to its own members; platform admins
            // (who hold a platform membership) can still get in to manage it.
            if ($tenant->status !== 'active' && ! $platformMembership) {
                abort(403, 'This company account is currently deactivated.');
            }

            $tenantContext->setTenant($tenant->id, false);
        } else {
            if (! $platformMembership) {
                abort(403, 'Platform administrators only.');
            }

            $tenantContext->setTenant(null, true);
        }

        return $next($request);
    }
}

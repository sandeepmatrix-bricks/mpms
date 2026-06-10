<?php

namespace App\Http\Middleware;

use App\Services\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the company for the /company/* area from the LOGGED-IN USER (not the
 * URL — there is no company slug in these routes). One user belongs to one
 * company. Sets the tenant scope and shares $company with every company view.
 */
class ResolveCompany
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('company.login');
        }

        $membership = $user->memberships()
            ->whereNotNull('tenant_id')
            ->with('tenant')
            ->first();

        $company = $membership?->tenant;

        if (! $company) {
            abort(403, 'Your account is not linked to any company.');
        }

        if ($company->status !== 'active') {
            abort(403, 'This company account is currently deactivated.');
        }

        app(TenantContext::class)->setTenant($company->id, false);
        View::share('company', $company);

        return $next($request);
    }
}

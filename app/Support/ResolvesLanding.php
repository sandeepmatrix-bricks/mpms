<?php

namespace App\Support;

use App\Models\User;

/**
 * Shared "where does this account belong after auth?" logic.
 *  - platform admins  -> the platform console
 *  - company members  -> their company portal
 *  - otherwise null (no portal access)
 */
trait ResolvesLanding
{
    protected function landingRoute(User $user): ?string
    {
        if ($user->is_admin) {
            return route('admin.dashboard');
        }

        $membership = $user->memberships()
            ->whereNotNull('tenant_id')
            ->with('tenant')
            ->first();

        if ($membership && $membership->tenant) {
            return route('company.dashboard');
        }

        return null;
    }
}

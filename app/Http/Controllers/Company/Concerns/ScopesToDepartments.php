<?php

namespace App\Http\Controllers\Company\Concerns;

use App\Models\Membership;
use App\Models\Tenant;
use Illuminate\Http\Request;

/**
 * Department (job-category) scoping for company controllers. A user assigned to
 * specific departments (membership.job_category_ids) only sees and manages
 * records inside those departments; an unrestricted user (e.g. the company
 * Admin, with no departments assigned) sees everything.
 */
trait ScopesToDepartments
{
    /**
     * Departments the logged-in user is restricted to. Returns null when the
     * user is unrestricted — meaning "see every department".
     */
    protected function assignedDepartments(Request $request, Tenant $company): ?array
    {
        $membership = Membership::where('tenant_id', $company->id)
            ->where('user_id', $request->user()->id)
            ->first();

        $ids = $membership?->job_category_ids ?? [];

        return empty($ids) ? null : array_values($ids);
    }

    /** Guard: 404 unless the given department id is within the user's scope. */
    protected function ensureDepartmentInScope(?array $assigned, ?string $categoryId): void
    {
        if ($assigned === null) {
            return;
        }

        abort_unless($categoryId !== null && in_array($categoryId, $assigned, true), 404);
    }
}

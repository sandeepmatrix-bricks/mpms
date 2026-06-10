<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ApplicantComment;
use App\Models\JobApplicant;
use App\Models\JobCategory;
use App\Models\JobDescription;
use App\Models\JobListing;
use App\Models\Membership;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Company dashboard. All figures are scoped to the departments the logged-in
     * user may see: an Admin (no assigned departments) gets the whole company —
     * the "main" dashboard — while a department-restricted user (e.g. Riddhi)
     * only sees records inside her assigned departments. Recent / user activity
     * is shown on the main dashboard only.
     */
    public function index(Request $request): View
    {
        $company = $request->user()->company();
        $assigned = $this->assignedDepartments($request, $company);
        $isMain = $assigned === null; // unrestricted Admin

        // ---- Summary cards (department-scoped) ----
        $stats = [
            'departments' => JobCategory::where('tenant_id', $company->id)
                ->when(! $isMain, fn ($q) => $q->whereIn('id', $assigned))
                ->count(),

            'designations' => JobListing::where('tenant_id', $company->id)
                ->when(! $isMain, fn ($q) => $q->whereIn('job_category_id', $assigned))
                ->count(),

            'job_descriptions' => JobDescription::where('tenant_id', $company->id)
                ->when(! $isMain, fn ($q) => $q->whereHas('listing',
                    fn ($l) => $l->whereIn('job_category_id', $assigned)))
                ->count(),

            'applicants' => $this->applicants($company, $isMain, $assigned)->count(),
        ];

        // ---- Applicant status (pie) ----
        $statusLabels = JobApplicant::statuses($company->id);
        $statusColors = JobApplicant::statusColors($company->id);

        $statusChart = $this->applicants($company, $isMain, $assigned)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->map(fn ($total, $slug) => [
                'label' => $slug ? ($statusLabels[$slug] ?? ucfirst(str_replace('_', ' ', $slug))) : 'No Status',
                'total' => (int) $total,
                'color' => $slug ? ($statusColors[$slug] ?? '#6c757d') : '#cbd5e1',
            ])
            ->values();

        // ---- Department-wise & Job role-wise applicants (donuts) ----
        $deptWise = $this->groupApplicants($company, $isMain, $assigned, 'job_categories.name');
        $roleWise = $this->groupApplicants($company, $isMain, $assigned, 'job_listings.job_role');

        // ---- Recent + per-user activity (MAIN dashboard only) ----
        $recentActivity = collect();
        $userActivity = collect();

        if ($isMain) {
            $recentActivity = ActivityLog::where('tenant_id', $company->id)
                ->with('user')->latest()->limit(8)->get();

            $userActivity = $this->userActivity($company);
        }

        return view('company.dashboard', compact(
            'company', 'stats', 'isMain', 'statusChart', 'deptWise', 'roleWise',
            'recentActivity', 'userActivity'
        ));
    }

    /** Final-submitted applicants for the company, scoped to the user's departments. */
    private function applicants(Tenant $company, bool $isMain, ?array $assigned)
    {
        return JobApplicant::where('tenant_id', $company->id)
            ->where('final_submit', true)
            ->when(! $isMain, fn ($q) => $q->whereHas('listing',
                fn ($l) => $l->whereIn('job_category_id', $assigned)));
    }

    /**
     * Applicant counts grouped by a department or job-role column. Returns a
     * collection of ['name' => ..., 'total' => ...], biggest first.
     */
    private function groupApplicants(Tenant $company, bool $isMain, ?array $assigned, string $groupCol)
    {
        return JobApplicant::query()
            ->join('job_listings', 'job_applicants.job_listing_id', '=', 'job_listings.id')
            ->join('job_categories', 'job_listings.job_category_id', '=', 'job_categories.id')
            ->where('job_applicants.tenant_id', $company->id)
            ->where('job_applicants.final_submit', true)
            ->whereNull('job_listings.deleted_at')
            ->whereNull('job_categories.deleted_at')
            ->when(! $isMain, fn ($q) => $q->whereIn('job_listings.job_category_id', $assigned))
            ->selectRaw("{$groupCol} as name, COUNT(*) as total")
            ->groupBy('name')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($r) => ['name' => $r->name ?? '—', 'total' => (int) $r->total]);
    }

    /** Comments and status changes per team member, busiest first. */
    private function userActivity(Tenant $company)
    {
        $comments = ApplicantComment::where('tenant_id', $company->id)
            ->where('is_deleted', false)
            ->selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')->pluck('total', 'user_id');

        $changes = ActivityLog::where('tenant_id', $company->id)
            ->where('action', 'status_change')
            ->selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')->pluck('total', 'user_id');

        $userIds = $comments->keys()->merge($changes->keys())->filter()->unique();
        $names = User::whereIn('id', $userIds)->pluck('name', 'id');

        return $userIds
            ->map(fn ($id) => [
                'name' => $names[$id] ?? 'Unknown',
                'comments' => (int) ($comments[$id] ?? 0),
                'changes' => (int) ($changes[$id] ?? 0),
            ])
            ->sortByDesc(fn ($r) => $r['comments'] + $r['changes'])
            ->values();
    }

    /**
     * Departments the logged-in user is restricted to (membership job_category_ids).
     * Returns null when unrestricted (Admin) — meaning "see everything".
     */
    private function assignedDepartments(Request $request, Tenant $company): ?array
    {
        $membership = Membership::where('tenant_id', $company->id)
            ->where('user_id', $request->user()->id)
            ->first();

        $ids = $membership?->job_category_ids ?? [];

        return empty($ids) ? null : array_values($ids);
    }
}

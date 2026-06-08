<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Membership;
use App\Models\Page;
use App\Models\PageBlock;
use App\Models\Record;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Platform overview. The EnsureAdmin middleware has put the request in
     * platform mode, so the tenant global scope is bypassed and these counts
     * span every company.
     */
    public function index(): View
    {
        $stats = [
            'tenants' => Tenant::count(),
            'users' => User::count(),
            'pages' => Page::count(),
            'collections' => Collection::count(),
            'records' => Record::count(),
            'memberships' => Membership::count(),
        ];

        $tenants = Tenant::query()
            ->withCount(['pages', 'collections'])
            ->orderBy('name')
            ->get()
            ->map(function (Tenant $tenant): Tenant {
                $tenant->records_count = Record::where('tenant_id', $tenant->id)->count();

                return $tenant;
            });

        // Sections grouped by type across every company (pie chart).
        $sectionsByType = PageBlock::query()
            ->selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        return view('admin.dashboard', compact('stats', 'tenants', 'sectionsByType'));
    }
}

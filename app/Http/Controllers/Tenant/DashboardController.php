<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Page;
use App\Models\PageBlock;
use App\Models\Record;
use App\Models\Tenant;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Company overview. ResolveTenant has scoped TenantContext to this tenant,
     * so every query below is automatically filtered to the current company.
     */
    public function index(Tenant $tenant): View
    {
        $stats = [
            'pages' => Page::count(),
            'sections' => PageBlock::count(),
            'collections' => Collection::count(),
            'records' => Record::count(),
        ];

        // Sections grouped by type (for the pie chart).
        $sectionsByType = PageBlock::query()
            ->selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        // Records per collection (donut).
        $recordsPerCollection = Collection::withCount('records')
            ->orderByDesc('records_count')
            ->get()
            ->map(fn (Collection $c) => ['name' => $c->name, 'total' => $c->records_count]);

        // Sections per page (donut) — only pages that actually have sections.
        $sectionsPerPage = Page::withCount('pageBlocks')
            ->orderByDesc('page_blocks_count')
            ->get()
            ->filter(fn (Page $p) => $p->page_blocks_count > 0)
            ->map(fn (Page $p) => ['name' => $p->title, 'total' => $p->page_blocks_count])
            ->values();

        return view('tenant.dashboard', compact(
            'tenant', 'stats', 'sectionsByType', 'recordsPerCollection', 'sectionsPerPage'
        ));
    }
}

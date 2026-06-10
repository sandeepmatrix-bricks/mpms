<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Tenant;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Platform overview — companies, their status, and which user runs each one.
     */
    public function index(): View
    {
        $stats = [
            'companies' => Tenant::count(),
            'active' => Tenant::where('status', 'active')->count(),
            'inactive' => Tenant::where('status', 'inactive')->count(),
        ];

        // Companies with their allocated owner (which user handles which company).
        $companies = Tenant::with('ownerMembership.user')->latest()->take(10)->get();

        $recentActivity = ActivityLog::with(['user', 'tenant'])->latest()->limit(12)->get();

        // New companies per month over the last 6 months (growth trend).
        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i)->startOfMonth());
        $recent = Tenant::where('created_at', '>=', $months->first())->get(['created_at']);
        $signups = $months->map(fn ($m) => [
            'label' => $m->format('M'),
            'total' => $recent->filter(fn ($t) => $t->created_at?->isSameMonth($m))->count(),
        ]);

        return view('admin.dashboard', compact('stats', 'companies', 'recentActivity', 'signups'));
    }
}

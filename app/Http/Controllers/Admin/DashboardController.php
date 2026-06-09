<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Platform overview — a clean summary of companies and users.
     */
    public function index(): View
    {
        $stats = [
            'companies' => Tenant::count(),
            'active' => Tenant::where('status', 'active')->count(),
            'inactive' => Tenant::where('status', 'inactive')->count(),
            'users' => User::where('is_admin', false)->count(),
        ];

        $companies = Tenant::withCount('memberships')->latest()->take(8)->get();

        $recentActivity = ActivityLog::with(['user', 'tenant'])->latest()->limit(12)->get();

        return view('admin.dashboard', compact('stats', 'companies', 'recentActivity'));
    }
}

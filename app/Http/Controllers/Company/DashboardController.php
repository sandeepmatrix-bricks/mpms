<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $company = $request->user()->company();

        $stats = [
            'users' => $company->memberships()->count(),
            'roles' => Role::where('tenant_id', $company->id)->count(),
        ];

        $recentActivity = ActivityLog::where('tenant_id', $company->id)
            ->with('user')->latest()->limit(8)->get();

        return view('company.dashboard', compact('company', 'stats', 'recentActivity'));
    }
}

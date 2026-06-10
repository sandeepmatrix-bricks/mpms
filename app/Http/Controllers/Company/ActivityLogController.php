<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Company audit trail — logins and key actions by the company's users.
 */
class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $company = $this->company($request);

        $logs = ActivityLog::where('tenant_id', $company->id)
            ->with('user')
            ->latest()
            ->limit(1000)
            ->get();

        return view('company.activity_logs.index', compact('company', 'logs'));
    }

    private function company(Request $request): Tenant
    {
        return $request->user()->company();
    }
}

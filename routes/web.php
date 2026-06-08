<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MembershipController;
use App\Http\Controllers\Admin\PageBlockController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RecordController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Tenant\CollectionController as TenantCollectionController;
use App\Http\Controllers\Tenant\DashboardController as TenantDashboardController;
use App\Http\Controllers\Tenant\PageBlockController as TenantPageBlockController;
use App\Http\Controllers\Tenant\PageController as TenantPageController;
use App\Http\Controllers\Tenant\RecordController as TenantRecordController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('admin.dashboard'));

/*
|--------------------------------------------------------------------------
| Platform admin panel
|--------------------------------------------------------------------------
| Simple is_admin auth. The EnsureAdmin middleware ('admin' alias) gates the
| panel and puts the request in platform mode so it sees every tenant.
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.attempt');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('tenants', TenantController::class)->except('show');
        Route::resource('users', UserController::class)->except('show');
        Route::resource('roles', RoleController::class)->except('show');
        Route::resource('permissions', PermissionController::class)->except('show');
        Route::resource('memberships', MembershipController::class)->except('show');
        Route::resource('pages', PageController::class)->except('show');
        Route::resource('page-blocks', PageBlockController::class)
            ->parameters(['page-blocks' => 'pageBlock'])->except('show');
        Route::resource('collections', CollectionController::class)->except('show');
        Route::resource('records', RecordController::class)->except('show');
    });
});

/*
|--------------------------------------------------------------------------
| Tenant portal
|--------------------------------------------------------------------------
| Per-company scoped admin. ResolveTenant sets TenantContext to this tenant
| (NOT platform mode), so the BelongsToTenant global scope filters everything
| to the current company. Access requires a membership for this tenant (or a
| platform membership). Registered AFTER the platform routes so literal admin
| paths (login, tenants, users, …) win over the {tenant:slug} segment.
*/
Route::middleware(['auth', 'resolve.tenant'])
    ->prefix('admin/{tenant:slug}')
    ->name('tenant.')
    ->group(function () {
        Route::get('/', [TenantDashboardController::class, 'index'])->name('dashboard');
        Route::resource('pages', TenantPageController::class)->except('show');
        Route::resource('page-blocks', TenantPageBlockController::class)
            ->parameters(['page-blocks' => 'pageBlock'])->except('show');
        Route::resource('collections', TenantCollectionController::class)->except('show');
        Route::resource('records', TenantRecordController::class)->except('show');
    });


<?php

namespace App\Providers;

use App\Observers\ActivityObserver;
use App\Services\TenantContext;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TenantContext::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // @permission('manage_users') ... @endpermission
        // Shows the block only if the current user holds the permission for the
        // tenant currently in context. Platform admins always pass (see
        // User::hasPermission). Supports dynamic, per-company permission keys.
        Blade::if('permission', function (string $key): bool {
            $user = auth()->user();

            return $user !== null && $user->hasPermission($key);
        });

        // Audit trail: log create/update/delete on the domain models.
        foreach (ActivityObserver::models() as $model) {
            $model::observe(ActivityObserver::class);
        }
    }
}

<?php

namespace App\Traits;

use App\Services\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 * @method static void addGlobalScope(string $identifier, \Closure $scope)
 * @method static void creating(callable $callback)
 */
trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder): void {
            $tenantContext = app(TenantContext::class);

            if ($tenantContext->isPlatform()) {
                return;
            }

            $tenantId = $tenantContext->tenantId();

            if ($tenantId === null) {
                $builder->whereRaw('0 = 1');
                return;
            }

            $model = $builder->getModel();

            if (method_exists($model, 'applyTenantScope')) {
                $model->applyTenantScope($builder, $tenantId);
                return;
            }

            $builder->where($model->qualifyColumn('tenant_id'), $tenantId);
        });

        static::creating(function (Model $model): void {
            $tenantContext = app(TenantContext::class);

            if ($tenantContext->isPlatform()) {
                return;
            }

            // Models that scope indirectly (e.g. PageBlock via its page) have no
            // tenant_id column of their own — don't try to fill it.
            if (method_exists($model, 'applyTenantScope')) {
                return;
            }

            if ($tenantContext->tenantId() !== null && blank($model->tenant_id)) {
                $model->tenant_id = $tenantContext->tenantId();
            }
        });
    }
}

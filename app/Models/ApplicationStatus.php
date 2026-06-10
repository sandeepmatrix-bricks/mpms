<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

/**
 * A hiring-pipeline status (master list). Drives the applicant Status dropdowns
 * and their colours. New statuses are added here, no code change needed.
 */
class ApplicationStatus extends Model
{
    use HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'tenant_id',
        'slug',
        'label',
        'color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Active statuses for a company (its own rows + the shared defaults), ordered.
     * Falls back to defaults if the table isn't migrated yet.
     *
     * @return \Illuminate\Support\Collection<int, self>
     */
    public static function forTenant(?string $tenantId)
    {
        if (! Schema::hasTable('application_statuses')) {
            return collect();
        }

        return static::query()
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('tenant_id')->orWhere('tenant_id', $tenantId))
            ->orderBy('sort_order')
            ->get();
    }
}

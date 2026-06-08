<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class PageBlock extends Model
{
    use HasFactory, HasUuids, BelongsToTenant;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'page_id',
        'type',
        'region',
        'position',
        'config',
        'data_source',
        'content',
    ];

    protected $casts = [
        'config' => 'array',
        'data_source' => 'array',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function applyTenantScope(Builder $builder, string $tenantId): void
    {
        $builder->whereHas('page', function (Builder $query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * A single audit-trail entry — who did what, when, from where.
 */
class ActivityLog extends Model
{
    use HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'ip',
        'user_agent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Convenience recorder. Resolves the current user/company/request when not given.
     */
    public static function record(string $action, ?string $description = null, array $attributes = []): self
    {
        $user = $attributes['user'] ?? Auth::user();
        $tenantId = $attributes['tenant_id'] ?? $user?->company()?->id;
        $request = request();

        return static::create([
            'tenant_id' => $tenantId,
            'user_id' => $user?->id,
            'action' => $action,
            'description' => $description,
            'subject_type' => $attributes['subject_type'] ?? null,
            'subject_id' => $attributes['subject_id'] ?? null,
            'ip' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }
}

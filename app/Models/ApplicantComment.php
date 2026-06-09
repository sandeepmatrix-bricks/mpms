<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A message in an applicant's Activity Chat. `mentions` holds the user ids tagged
 * with @ inside the body; each also gets a row in the mentions table.
 */
class ApplicantComment extends Model
{
    use HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'tenant_id',
        'job_applicant_id',
        'user_id',
        'body',
        'attachment',
        'attachment_type',
        'mentions',
        'edited_at',
        'is_deleted',
        'deleted_for',
    ];

    protected $casts = [
        'mentions' => 'array',
        'deleted_for' => 'array',
        'edited_at' => 'datetime',
        'is_deleted' => 'boolean',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(JobApplicant::class, 'job_applicant_id');
    }

    public function mentionRows(): HasMany
    {
        return $this->hasMany(Mention::class, 'comment_id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(CommentReaction::class, 'comment_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One person @mentioned inside an Activity Chat message. Drives the header
 * notification bell (unread count + list) and the mention email.
 */
class Mention extends Model
{
    use HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'tenant_id',
        'comment_id',
        'job_applicant_id',
        'mentioned_user_id',
        'mentioned_by_user_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function comment(): BelongsTo
    {
        return $this->belongsTo(ApplicantComment::class, 'comment_id');
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(JobApplicant::class, 'job_applicant_id');
    }

    public function mentionedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentioned_by_user_id');
    }
}

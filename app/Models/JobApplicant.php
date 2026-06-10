<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A candidate's application to one of a company's roles. final_submit splits
 * completed applications ("Job Applicant") from drafts ("Incomplete Record").
 */
class JobApplicant extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'tenant_id',
        'job_listing_id',
        'name',
        'profile_image',
        'email',
        'phone',
        'gender',
        'address',
        'source',
        'position',
        'resume',
        'cover_letter',
        'video_resume',
        'intro_video',
        'screening_video',
        'portfolio',
        'portfolio_url',
        'answers',
        'education',
        'work_experience',
        'final_submit',
        'status',
    ];

    protected $casts = [
        'answers' => 'array',
        'education' => 'array',
        'work_experience' => 'array',
        'final_submit' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(JobListing::class, 'job_listing_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ApplicantComment::class, 'job_applicant_id');
    }

    /** Hiring-pipeline statuses (slug => label) from the master table. */
    public static function statuses(?string $tenantId = null): array
    {
        $rows = ApplicationStatus::forTenant($tenantId);

        if ($rows->isEmpty()) {
            return self::fallbackStatuses();
        }

        return $rows->pluck('label', 'slug')->all();
    }

    /** Status colours (slug => hex) from the master table. */
    public static function statusColors(?string $tenantId = null): array
    {
        $rows = ApplicationStatus::forTenant($tenantId);

        return $rows->isEmpty()
            ? array_fill_keys(array_keys(self::fallbackStatuses()), '#6c757d')
            : $rows->pluck('color', 'slug')->all();
    }

    /** Used only before the master table is migrated/seeded. */
    private static function fallbackStatuses(): array
    {
        return [
            'new' => 'New', 'rejected' => 'Rejected', 'shortlisted' => 'Shortlisted',
            'hired' => 'Hired', 'blacklist' => 'Blacklist', 'push_to_hr' => 'Push to HR',
            'in_pipeline' => 'In Pipeline', 'contacted' => 'Contacted', 'may_be' => 'May be',
            'pinned' => 'Pinned', 'interview_scheduled' => 'Interview Scheduled',
            'interviewed' => 'Interviewed', 'interviewed_rejected' => 'Interviewed Rejected',
            'schedule_interview' => 'Schedule Interview',
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A "Job Description" — the detailed posting (responsibilities, skills,
 * dynamic application questions) attached to a designation (JobListing),
 * belonging to one company (tenant).
 */
class JobDescription extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'tenant_id',
        'job_listing_id',
        'banner_heading',
        'banner_image',
        'section_heading',
        'designation',
        'location',
        'experience_required',
        'working_days',
        'job_type',
        'reporting_to',
        'about_role',
        'key_responsibilities',
        'key_skills_competencies',
        'success_looks',
        'qualification',
        'job_questions',
        'status',
    ];

    protected $casts = [
        'job_questions' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(JobListing::class, 'job_listing_id');
    }
}

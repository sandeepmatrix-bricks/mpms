<?php

namespace Database\Seeders;

use App\Models\ApplicationStatus;
use Illuminate\Database\Seeder;

/**
 * Shared (tenant_id = null) hiring-pipeline statuses with their colours.
 * Colours mirror the reference project's status map.
 */
class ApplicationStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['new', 'New', '#6c757d'],
            ['rejected', 'Rejected', '#d04451'],
            ['shortlisted', 'Shortlisted', '#53c28b'],
            ['hired', 'Hired', '#20905d'],
            ['blacklist', 'Blacklist', '#45534d'],
            ['push_to_hr', 'Push to HR', '#537fbd'],
            ['in_pipeline', 'In Pipeline', '#cd9c00'],
            ['contacted', 'Contacted', '#2286a5'],
            ['may_be', 'May be', '#440480'],
            ['pinned', 'Pinned', '#ee8f9e'],
            ['interview_scheduled', 'Interview Scheduled', '#e0863a'],
            ['interviewed', 'Interviewed', '#7a4fc0'],
            ['interviewed_rejected', 'Interviewed Rejected', '#16a085'],
            ['schedule_interview', 'Schedule Interview', '#9c5a6e'],
        ];

        foreach ($statuses as $i => [$slug, $label, $color]) {
            ApplicationStatus::updateOrCreate(
                ['tenant_id' => null, 'slug' => $slug],
                ['label' => $label, 'color' => $color, 'sort_order' => $i, 'is_active' => true],
            );
        }
    }
}

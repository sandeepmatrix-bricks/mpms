<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\CareerPage;
use App\Models\JobCategory;
use App\Models\JobDescription;
use App\Models\JobListing;
use App\Models\Role;
use App\Models\User;
use App\Models\ApplicationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Logs create / update / delete on the domain models to the activity trail.
 * Only records when there is a logged-in user (so seeders/console stay silent).
 * Applicant status changes and chat comments are logged explicitly elsewhere.
 */
class ActivityObserver
{
    private const LABELS = [
        User::class => 'User',
        Role::class => 'Role',
        JobCategory::class => 'Department',
        JobListing::class => 'Designation',
        JobDescription::class => 'Job description',
        CareerPage::class => 'Career main page',
        ApplicationStatus::class => 'Status',
    ];

    /** Models this observer is registered for. */
    public static function models(): array
    {
        return array_keys(self::LABELS);
    }

    public function created(Model $model): void
    {
        $this->log('create', 'Created', $model);
    }

    public function updated(Model $model): void
    {
        $this->log('update', 'Updated', $model);
    }

    public function deleted(Model $model): void
    {
        $this->log('delete', 'Deleted', $model);
    }

    private function log(string $action, string $verb, Model $model): void
    {
        if (! Auth::check()) {
            return;
        }

        $label = self::LABELS[$model::class] ?? class_basename($model);
        $name = $model->name ?? $model->job_role ?? $model->label ?? $model->title ?? '';

        ActivityLog::record($action, trim("{$verb} {$label}: {$name}", ': '), [
            'subject_type' => $model::class,
            'subject_id' => $model->getKey(),
        ]);
    }
}

<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\SubAdminController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\Company\ActivityLogController as CompanyActivityLogController;
use App\Http\Controllers\Company\ApplicantCommentController as CompanyApplicantCommentController;
use App\Http\Controllers\Company\ApplicationStatusController as CompanyApplicationStatusController;
use App\Http\Controllers\Company\AuthController as CompanyAuthController;
use App\Http\Controllers\Company\CareerPageController as CompanyCareerPageController;
use App\Http\Controllers\Company\DashboardController as CompanyDashboardController;
use App\Http\Controllers\Company\MentionController as CompanyMentionController;
use App\Http\Controllers\Company\JobApplicantController as CompanyJobApplicantController;
use App\Http\Controllers\Company\JobCategoryController as CompanyJobCategoryController;
use App\Http\Controllers\Company\JobDescriptionController as CompanyJobDescriptionController;
use App\Http\Controllers\Company\JobListingController as CompanyJobListingController;
use App\Http\Controllers\Company\RoleController as CompanyRoleController;
use App\Http\Controllers\Company\SettingsController as CompanySettingsController;
use App\Http\Controllers\Company\UserController as CompanyUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('admin.login'));

/*
|--------------------------------------------------------------------------
| First-login password change (shared by both areas)
|--------------------------------------------------------------------------
| Any authenticated user flagged `must_change_password` is forced here by the
| EnsurePasswordChanged middleware until they set their own password.
*/
Route::middleware('auth')->group(function () {
    Route::get('password/change', [PasswordChangeController::class, 'show'])->name('password.change');
    Route::post('password/change', [PasswordChangeController::class, 'update'])->name('password.change.update');
});

/*
|--------------------------------------------------------------------------
| SUPER ADMIN AREA  ( /admin )
|--------------------------------------------------------------------------
| Separate login + console. EnsureAdmin gates the panel (is_admin only) and
| puts the request in platform mode (sees every company).
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('login.attempt');
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

    /* Self-service password reset. */
    Route::get('password/forgot', [AdminAuthController::class, 'showForgot'])->name('password.request');
    Route::post('password/forgot', [AdminAuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('password/reset/{token}', [AdminAuthController::class, 'showReset'])->name('password.reset');
    Route::post('password/reset', [AdminAuthController::class, 'resetPassword'])->name('password.update');

    Route::middleware('admin')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        /* Companies — gated by the companies module (read/write/edit/delete). */
        Route::middleware('permission:companies.read')->group(function () {
            Route::get('companies', [CompanyController::class, 'index'])->name('companies.index');
            Route::get('companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
        });
        Route::middleware('permission:companies.write')->group(function () {
            Route::get('companies/create', [CompanyController::class, 'create'])->name('companies.create');
            Route::post('companies', [CompanyController::class, 'store'])->name('companies.store');
            Route::get('companies/{company}/setup', [CompanyController::class, 'setupAdmin'])->name('companies.setup');
            Route::post('companies/{company}/setup', [CompanyController::class, 'storeAdmin'])->name('companies.setup.store');
            Route::get('companies/{company}/credentials', [CompanyController::class, 'credentials'])->name('companies.credentials');
        });
        Route::middleware('permission:companies.edit')->group(function () {
            Route::put('companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
            Route::get('companies/{company}/reset-password', [CompanyController::class, 'resetPasswordForm'])->name('companies.reset-password');
            Route::put('companies/{company}/reset-password', [CompanyController::class, 'resetPassword'])->name('companies.reset-password.update');
        });
        Route::post('companies/{company}/toggle-status', [CompanyController::class, 'toggleStatus'])
            ->middleware('permission:companies.deactivate')->name('companies.toggle-status');
        Route::delete('companies/{company}', [CompanyController::class, 'destroy'])
            ->middleware('permission:companies.delete')->name('companies.destroy');

        /* Company Users — create logins and assign them to a company. */
        Route::middleware('permission:users.read')->group(function () {
            Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        });
        Route::middleware('permission:users.write')->group(function () {
            Route::get('users/create', [AdminUserController::class, 'create'])->name('users.create');
            Route::post('users', [AdminUserController::class, 'store'])->name('users.store');
        });
        Route::middleware('permission:users.edit')->group(function () {
            Route::get('users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
            Route::put('users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        });
        Route::delete('users/{user}', [AdminUserController::class, 'destroy'])
            ->middleware('permission:users.delete')->name('users.destroy');

        /* Sub Admins — gated by the sub_admins module. */
        Route::middleware('permission:sub_admins.read')->group(function () {
            Route::get('sub-admins', [SubAdminController::class, 'index'])->name('sub-admins.index');
        });
        Route::middleware('permission:sub_admins.write')->group(function () {
            Route::get('sub-admins/create', [SubAdminController::class, 'create'])->name('sub-admins.create');
            Route::post('sub-admins', [SubAdminController::class, 'store'])->name('sub-admins.store');
        });
        Route::middleware('permission:sub_admins.edit')->group(function () {
            Route::get('sub-admins/{subAdmin}/edit', [SubAdminController::class, 'edit'])->name('sub-admins.edit');
            Route::put('sub-admins/{subAdmin}', [SubAdminController::class, 'update'])->name('sub-admins.update');
        });
        Route::delete('sub-admins/{subAdmin}', [SubAdminController::class, 'destroy'])
            ->middleware('permission:sub_admins.delete')->name('sub-admins.destroy');

        /* Roles (for Sub Admins) — gated by the roles module. */
        Route::middleware('permission:roles.read')->group(function () {
            Route::get('roles', [AdminRoleController::class, 'index'])->name('roles.index');
        });
        Route::middleware('permission:roles.write')->group(function () {
            Route::get('roles/create', [AdminRoleController::class, 'create'])->name('roles.create');
            Route::post('roles', [AdminRoleController::class, 'store'])->name('roles.store');
        });
        Route::middleware('permission:roles.edit')->group(function () {
            Route::get('roles/{role}/edit', [AdminRoleController::class, 'edit'])->name('roles.edit');
            Route::put('roles/{role}', [AdminRoleController::class, 'update'])->name('roles.update');
        });
        Route::delete('roles/{role}', [AdminRoleController::class, 'destroy'])
            ->middleware('permission:roles.delete')->name('roles.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| COMPANY AREA  ( /company )
|--------------------------------------------------------------------------
| Separate login + clean dashboard. ResolveCompany takes the company from the
| logged-in user (one company per user) — no company id in the URL — and scopes
| everything to it. Tabs are gated by per-company permissions.
*/
Route::prefix('company')->name('company.')->group(function () {
    Route::get('login', [CompanyAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [CompanyAuthController::class, 'login'])->name('login.attempt');
    Route::post('logout', [CompanyAuthController::class, 'logout'])->name('logout');

    /* Self-service password reset. */
    Route::get('password/forgot', [CompanyAuthController::class, 'showForgot'])->name('password.request');
    Route::post('password/forgot', [CompanyAuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('password/reset/{token}', [CompanyAuthController::class, 'showReset'])->name('password.reset');
    Route::post('password/reset', [CompanyAuthController::class, 'resetPassword'])->name('password.update');

    Route::middleware(['auth', 'resolve.company'])->group(function () {
        Route::get('/', [CompanyDashboardController::class, 'index'])->name('dashboard');

        /* Team members (Users) — gated per action (read/write/edit/delete). */
        Route::middleware('permission:users.read')->group(function () {
            Route::get('users', [CompanyUserController::class, 'index'])->name('users.index');
        });
        Route::middleware('permission:users.write')->group(function () {
            Route::get('users/create', [CompanyUserController::class, 'create'])->name('users.create');
            Route::post('users', [CompanyUserController::class, 'store'])->name('users.store');
        });
        Route::middleware('permission:users.edit')->group(function () {
            Route::get('users/{user}/edit', [CompanyUserController::class, 'edit'])->name('users.edit');
            Route::put('users/{user}', [CompanyUserController::class, 'update'])->name('users.update');
        });
        Route::delete('users/{user}', [CompanyUserController::class, 'destroy'])
            ->middleware('permission:users.delete')->name('users.destroy');

        /* Roles & Permissions — gated per action. */
        Route::middleware('permission:roles.read')->group(function () {
            Route::get('roles', [CompanyRoleController::class, 'index'])->name('roles.index');
        });
        Route::middleware('permission:roles.write')->group(function () {
            Route::get('roles/create', [CompanyRoleController::class, 'create'])->name('roles.create');
            Route::post('roles', [CompanyRoleController::class, 'store'])->name('roles.store');
        });
        Route::middleware('permission:roles.edit')->group(function () {
            Route::get('roles/{role}/edit', [CompanyRoleController::class, 'edit'])->name('roles.edit');
            Route::put('roles/{role}', [CompanyRoleController::class, 'update'])->name('roles.update');
        });
        Route::delete('roles/{role}', [CompanyRoleController::class, 'destroy'])
            ->middleware('permission:roles.delete')->name('roles.destroy');

        /* Job Management → Departments (job categories) — gated per action. */
        Route::middleware('permission:job_categories.read')->group(function () {
            Route::get('job-categories', [CompanyJobCategoryController::class, 'index'])->name('job-categories.index');
        });
        Route::middleware('permission:job_categories.write')->group(function () {
            Route::get('job-categories/create', [CompanyJobCategoryController::class, 'create'])->name('job-categories.create');
            Route::post('job-categories', [CompanyJobCategoryController::class, 'store'])->name('job-categories.store');
        });
        Route::middleware('permission:job_categories.edit')->group(function () {
            Route::get('job-categories/{jobCategory}/edit', [CompanyJobCategoryController::class, 'edit'])->name('job-categories.edit');
            Route::put('job-categories/{jobCategory}', [CompanyJobCategoryController::class, 'update'])->name('job-categories.update');
        });
        Route::delete('job-categories/{jobCategory}', [CompanyJobCategoryController::class, 'destroy'])
            ->middleware('permission:job_categories.delete')->name('job-categories.destroy');

        /* Job Management → Main Page (careers landing content) — gated by the jobs module. */
        Route::middleware('permission:jobs.read')->group(function () {
            Route::get('main-page', [CompanyCareerPageController::class, 'edit'])->name('career-page.edit');
        });
        Route::put('main-page', [CompanyCareerPageController::class, 'update'])
            ->middleware('permission:jobs.edit')->name('career-page.update');

        /* Job Management → Designations + Job Descriptions — gated by the jobs module. */
        Route::middleware('permission:jobs.read')->group(function () {
            Route::get('job-listings', [CompanyJobListingController::class, 'index'])->name('job-listings.index');
            Route::get('job-descriptions', [CompanyJobDescriptionController::class, 'index'])->name('job-descriptions.index');
        });
        Route::middleware('permission:jobs.write')->group(function () {
            Route::get('job-listings/create', [CompanyJobListingController::class, 'create'])->name('job-listings.create');
            Route::post('job-listings', [CompanyJobListingController::class, 'store'])->name('job-listings.store');
            Route::get('job-descriptions/create', [CompanyJobDescriptionController::class, 'create'])->name('job-descriptions.create');
            Route::post('job-descriptions', [CompanyJobDescriptionController::class, 'store'])->name('job-descriptions.store');
        });
        Route::middleware('permission:jobs.edit')->group(function () {
            Route::get('job-listings/{jobListing}/edit', [CompanyJobListingController::class, 'edit'])->name('job-listings.edit');
            Route::put('job-listings/{jobListing}', [CompanyJobListingController::class, 'update'])->name('job-listings.update');
            Route::get('job-descriptions/{jobDescription}/edit', [CompanyJobDescriptionController::class, 'edit'])->name('job-descriptions.edit');
            Route::put('job-descriptions/{jobDescription}', [CompanyJobDescriptionController::class, 'update'])->name('job-descriptions.update');
        });
        Route::middleware('permission:jobs.delete')->group(function () {
            Route::delete('job-listings/{jobListing}', [CompanyJobListingController::class, 'destroy'])->name('job-listings.destroy');
            Route::delete('job-descriptions/{jobDescription}', [CompanyJobDescriptionController::class, 'destroy'])->name('job-descriptions.destroy');
        });

        /* Job Applicants + Incomplete Records — gated by the applicants module. */
        Route::middleware('permission:applicants.read')->group(function () {
            Route::match(['get', 'post'], 'applicants', [CompanyJobApplicantController::class, 'index'])->name('applicants.index');
            Route::match(['get', 'post'], 'applicants/incomplete', [CompanyJobApplicantController::class, 'incomplete'])->name('applicants.incomplete');
            Route::get('applicants-locations', [CompanyJobApplicantController::class, 'locations'])->name('applicants.locations');
            Route::get('applicants/{jobApplicant}', [CompanyJobApplicantController::class, 'show'])->name('applicants.show');

            // Activity Chat (comments + @mentions + reactions + edit/delete).
            Route::get('applicants/{jobApplicant}/comments', [CompanyApplicantCommentController::class, 'index'])->name('applicants.comments.index');
            Route::post('applicants/{jobApplicant}/comments', [CompanyApplicantCommentController::class, 'store'])->name('applicants.comments.store');
            Route::put('applicants/{jobApplicant}/comments/{comment}', [CompanyApplicantCommentController::class, 'update'])->name('applicants.comments.update');
            Route::delete('applicants/{jobApplicant}/comments/{comment}', [CompanyApplicantCommentController::class, 'destroy'])->name('applicants.comments.destroy');
            Route::post('applicants/{jobApplicant}/comments/{comment}/delete-for-me', [CompanyApplicantCommentController::class, 'deleteForMe'])->name('applicants.comments.delete-for-me');
            Route::post('applicants/{jobApplicant}/comments/{comment}/react', [CompanyApplicantCommentController::class, 'react'])->name('applicants.comments.react');
        });
        // Manually add an applicant (HR entry) — gated by applicants.write.
        // Distinct path so it doesn't collide with the POST filter on /applicants.
        Route::post('applicants/store', [CompanyJobApplicantController::class, 'store'])
            ->middleware('permission:applicants.write')->name('applicants.store');
        Route::put('applicants/{jobApplicant}/status', [CompanyJobApplicantController::class, 'updateStatus'])
            ->middleware('permission:applicants.edit')->name('applicants.status');
        Route::delete('applicants/{jobApplicant}', [CompanyJobApplicantController::class, 'destroy'])
            ->middleware('permission:applicants.delete')->name('applicants.destroy');

        /* Application Statuses (master dropdown) — gated by the statuses module. */
        Route::middleware('permission:statuses.read')->group(function () {
            Route::get('statuses', [CompanyApplicationStatusController::class, 'index'])->name('statuses.index');
        });
        Route::middleware('permission:statuses.write')->group(function () {
            Route::get('statuses/create', [CompanyApplicationStatusController::class, 'create'])->name('statuses.create');
            Route::post('statuses', [CompanyApplicationStatusController::class, 'store'])->name('statuses.store');
        });
        Route::middleware('permission:statuses.edit')->group(function () {
            Route::get('statuses/{status}/edit', [CompanyApplicationStatusController::class, 'edit'])->name('statuses.edit');
            Route::put('statuses/{status}', [CompanyApplicationStatusController::class, 'update'])->name('statuses.update');
        });
        Route::delete('statuses/{status}', [CompanyApplicationStatusController::class, 'destroy'])
            ->middleware('permission:statuses.delete')->name('statuses.destroy');

        /* Header notifications (@mentions) + audit trail. */
        Route::post('mentions/read', [CompanyMentionController::class, 'markAllRead'])->name('mentions.read');
        Route::get('activity-logs', [CompanyActivityLogController::class, 'index'])
            ->middleware('permission:users.read')->name('activity-logs.index');

        Route::middleware('permission:settings.read')->group(function () {
            Route::get('settings', [CompanySettingsController::class, 'edit'])->name('settings.edit');
        });
        Route::put('settings', [CompanySettingsController::class, 'update'])
            ->middleware('permission:settings.edit')->name('settings.update');
    });
});

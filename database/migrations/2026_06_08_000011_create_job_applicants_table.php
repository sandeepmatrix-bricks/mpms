<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Job Applicants (tenant-scoped). Candidates who applied to a company's roles.
 *
 * final_submit mirrors the reference's user_profiles.final_submit:
 *   1 → a completed application  → shown under "Job Applicant"
 *   0 → an unfinished draft      → shown under "Incomplete Record"
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applicants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('job_listing_id')->nullable()->constrained('job_listings')->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('position')->nullable();
            $table->string('resume')->nullable();
            $table->text('cover_letter')->nullable();
            $table->string('video_resume')->nullable();
            $table->string('portfolio')->nullable();
            $table->json('answers')->nullable();
            $table->boolean('final_submit')->default(false);
            $table->string('status')->default('new');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applicants');
    }
};

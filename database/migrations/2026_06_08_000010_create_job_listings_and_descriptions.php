<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Job Management (tenant-scoped):
 *  - job_listings    = "Designations" (the reference's career_category_listing):
 *                      a job role posted under a department (job_category).
 *  - job_descriptions = "Job Descriptions" (the reference's job_details):
 *                      the detailed posting (responsibilities, skills, dynamic
 *                      application questions) attached to a designation.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_listings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('job_category_id')->constrained('job_categories')->cascadeOnDelete();
            $table->string('job_role');
            $table->string('slug')->nullable();
            $table->string('banner_heading')->nullable();
            $table->text('section_heading')->nullable();
            $table->string('location')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('job_descriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('job_listing_id')->constrained('job_listings')->cascadeOnDelete();
            $table->string('banner_heading')->nullable();
            $table->string('section_heading')->nullable();
            $table->string('designation')->nullable();
            $table->string('location')->nullable();
            $table->string('experience_required')->nullable();
            $table->string('working_days')->nullable();
            $table->string('job_type')->nullable();
            $table->string('reporting_to')->nullable();
            $table->text('about_role')->nullable();
            $table->text('key_responsibilities')->nullable();
            $table->text('key_skills_competencies')->nullable();
            $table->text('success_looks')->nullable();
            $table->text('qualification')->nullable();
            $table->json('job_questions')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_descriptions');
        Schema::dropIfExists('job_listings');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Activity Chat on an applicant (applicant_comments) and the @mentions raised
 * inside those messages (mentions) — which drive the header notification bell
 * and the mention emails.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicant_comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('job_applicant_id')->constrained('job_applicants')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('body');
            $table->json('mentions')->nullable();
            $table->timestamps();

            $table->index(['job_applicant_id', 'created_at']);
        });

        Schema::create('mentions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('comment_id')->constrained('applicant_comments')->cascadeOnDelete();
            $table->foreignUuid('job_applicant_id')->constrained('job_applicants')->cascadeOnDelete();
            $table->foreignUuid('mentioned_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('mentioned_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['mentioned_user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentions');
        Schema::dropIfExists('applicant_comments');
    }
};

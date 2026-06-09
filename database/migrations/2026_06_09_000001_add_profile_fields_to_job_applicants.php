<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Richer applicant profile (mirrors the reference user_profiles JSON blobs) so the
 * "Applicant Details" page can show Personal Info, Documents & Videos and Education.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_applicants', function (Blueprint $table) {
            $table->string('gender')->nullable()->after('phone');
            $table->string('address')->nullable()->after('gender');
            $table->string('source')->nullable()->after('address');
            $table->string('intro_video')->nullable()->after('video_resume');
            $table->string('screening_video')->nullable()->after('intro_video');
            $table->string('portfolio_url')->nullable()->after('portfolio');
            $table->json('education')->nullable()->after('answers');
            $table->json('work_experience')->nullable()->after('education');
        });
    }

    public function down(): void
    {
        Schema::table('job_applicants', function (Blueprint $table) {
            $table->dropColumn([
                'gender', 'address', 'source', 'intro_video', 'screening_video',
                'portfolio_url', 'education', 'work_experience',
            ]);
        });
    }
};

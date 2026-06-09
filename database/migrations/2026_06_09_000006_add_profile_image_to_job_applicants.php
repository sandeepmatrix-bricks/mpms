<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Applicant profile photo (the reference's profile_images/*). Shown on the
 * "Applicant Details" Personal Info card.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_applicants', function (Blueprint $table) {
            $table->string('profile_image')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('job_applicants', function (Blueprint $table) {
            $table->dropColumn('profile_image');
        });
    }
};

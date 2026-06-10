<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Job descriptions carry their own banner image (the reference's job_details.banner_image),
 * shown on the "Banner Details" card of the Job Description form.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_descriptions', function (Blueprint $table) {
            $table->string('banner_image')->nullable()->after('banner_heading');
        });
    }

    public function down(): void
    {
        Schema::table('job_descriptions', function (Blueprint $table) {
            $table->dropColumn('banner_image');
        });
    }
};

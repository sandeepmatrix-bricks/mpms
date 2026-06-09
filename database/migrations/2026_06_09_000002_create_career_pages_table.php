<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Careers landing-page content (the reference's "Main Page Details"). One row per
 * company — the banner/intro shown on the public careers page. Tenant-scoped.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('career_pages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('page_title')->nullable();
            $table->string('icon_image')->nullable();
            $table->string('banner_image')->nullable();
            $table->text('banner_content')->nullable();
            $table->string('title')->nullable();
            $table->text('introduction')->nullable();
            $table->text('section_heading')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->unique('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_pages');
    }
};

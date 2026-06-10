<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Master list of hiring-pipeline statuses (slug, label, colour). Kept in a table
 * — not hard-coded — so new statuses can be added later without code changes.
 * tenant_id is null for the shared defaults; a company may add its own rows.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_statuses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            $table->string('slug');
            $table->string('label');
            $table->string('color', 20)->default('#6c757d');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_statuses');
    }
};

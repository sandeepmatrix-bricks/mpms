<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Job Departments (a.k.a. career categories) for the company area.
 *
 * Tenant-scoped: every department belongs to one company. Also adds a JSON
 * column on memberships so a company user can be assigned to one or more
 * departments (the "Job Department" multi-select on the Add User form).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'name']);
        });

        Schema::table('memberships', function (Blueprint $table) {
            $table->json('job_category_ids')->nullable()->after('role_id');
        });
    }

    public function down(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->dropColumn('job_category_ids');
        });

        Schema::dropIfExists('job_categories');
    }
};

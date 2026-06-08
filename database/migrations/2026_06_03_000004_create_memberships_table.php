<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('role_id')->constrained('roles')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'tenant_id', 'role_id']);
            // tenant_id is the base column of this generated column. MySQL forbids
            // ON DELETE CASCADE/SET NULL on the base column of a STORED generated
            // column, so this must be VIRTUAL to keep the cascade FK on tenant_id.
            $table->string('tenant_key', 36)->virtualAs("IFNULL(tenant_id,'00000000-0000-0000-0000-000000000000')");
            $table->unique(['user_id', 'role_id', 'tenant_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};

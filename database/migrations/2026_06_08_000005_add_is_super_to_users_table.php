<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * is_super  = the top platform owner (full access, bypasses permissions)
     * is_admin  = can sign into the /admin console (Super Admin AND Sub Admins)
     *
     * Existing admins become Super Admins so nothing they had access to breaks.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_super')->default(false)->after('is_admin');
        });

        DB::table('users')->where('is_admin', true)->update(['is_super' => true]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_super');
        });
    }
};

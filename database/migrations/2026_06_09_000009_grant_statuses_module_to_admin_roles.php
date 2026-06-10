<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * The new "statuses" company module appears on the Role screen automatically,
 * but existing company Admin roles were created before it existed — grant them
 * the full statuses RWED set so nothing silently loses access.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Ensure the permission rows exist.
        $permIds = [];
        foreach (['read', 'write', 'edit', 'delete'] as $action) {
            $key = "statuses.{$action}";
            $id = DB::table('permissions')->where('key', $key)->value('id');
            if (! $id) {
                $id = (string) Str::uuid();
                DB::table('permissions')->insert([
                    'id' => $id, 'key' => $key, 'created_at' => now(), 'updated_at' => now(),
                ]);
            }
            $permIds[] = $id;
        }

        // Grant to every tenant Admin role.
        $roleIds = DB::table('roles')
            ->whereNotNull('tenant_id')
            ->where('name', 'Admin')
            ->pluck('id');

        foreach ($roleIds as $roleId) {
            foreach ($permIds as $permId) {
                DB::table('role_permission')->updateOrInsert([
                    'role_id' => $roleId, 'permission_id' => $permId,
                ]);
            }
        }
    }

    public function down(): void
    {
        $permIds = DB::table('permissions')
            ->whereIn('key', ['statuses.read', 'statuses.write', 'statuses.edit', 'statuses.delete'])
            ->pluck('id');

        DB::table('role_permission')->whereIn('permission_id', $permIds)->delete();
    }
};

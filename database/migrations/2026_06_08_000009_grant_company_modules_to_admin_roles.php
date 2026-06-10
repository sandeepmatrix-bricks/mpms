<?php

use App\Support\Modules;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * New company modules (Job Departments, Job Management, Job Applicants) were
 * added after the per-company "Admin" roles already existed. This grants every
 * existing tenant Admin role the full Read/Write/Edit/Delete set for all company
 * modules, so company admins immediately see and manage the new tabs.
 */
return new class extends Migration
{
    public function up(): void
    {
        Modules::ensure(); // make sure every company permission row exists

        $permIdByKey = DB::table('permissions')->pluck('id', 'key');

        // Full RWED across every company module.
        $keys = [];
        foreach (array_keys(Modules::company()) as $module) {
            foreach (['read', 'write', 'edit', 'delete'] as $action) {
                $keys[] = "{$module}.{$action}";
            }
        }

        $adminRoleIds = DB::table('roles')
            ->whereNotNull('tenant_id')
            ->where('name', 'Admin')
            ->pluck('id');

        foreach ($adminRoleIds as $roleId) {
            foreach ($keys as $key) {
                if (! isset($permIdByKey[$key])) {
                    continue;
                }

                DB::table('role_permission')->updateOrInsert([
                    'role_id' => $roleId,
                    'permission_id' => $permIdByKey[$key],
                ]);
            }
        }
    }

    public function down(): void
    {
        // One-way grant; safe to leave in place.
    }
};

<?php

use App\Support\Modules;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The company area switched from flat permission keys
 * (manage_users / manage_roles / manage_settings / view_dashboard) to the same
 * module-driven Read/Write/Edit/Delete grid the admin side uses
 * (users.read, roles.edit, ...).
 *
 * This re-grants every per-company (tenant) role the RWED permissions that match
 * the flat keys it already held, so existing company logins keep their access
 * after the route middleware changed. The old flat rows are then detached.
 */
return new class extends Migration
{
    /** flat key => the RWED keys that replace it */
    private array $map = [
        'manage_users' => ['users.read', 'users.write', 'users.edit', 'users.delete'],
        'manage_roles' => ['roles.read', 'roles.write', 'roles.edit', 'roles.delete'],
        'manage_settings' => ['settings.read', 'settings.write', 'settings.edit', 'settings.delete'],
        'view_dashboard' => [], // dashboard is always visible now
    ];

    public function up(): void
    {
        // Make sure every company module/action permission row exists.
        Modules::ensure();

        $permIdByKey = DB::table('permissions')->pluck('id', 'key');

        // Per-company roles only (platform roles are untouched).
        $tenantRoleIds = DB::table('roles')->whereNotNull('tenant_id')->pluck('id');

        foreach ($tenantRoleIds as $roleId) {
            $heldKeys = DB::table('role_permission')
                ->join('permissions', 'permissions.id', '=', 'role_permission.permission_id')
                ->where('role_permission.role_id', $roleId)
                ->pluck('permissions.key')
                ->all();

            foreach ($this->map as $flatKey => $rwedKeys) {
                if (! in_array($flatKey, $heldKeys, true)) {
                    continue;
                }

                foreach ($rwedKeys as $newKey) {
                    if (! isset($permIdByKey[$newKey])) {
                        continue;
                    }

                    DB::table('role_permission')->updateOrInsert([
                        'role_id' => $roleId,
                        'permission_id' => $permIdByKey[$newKey],
                    ]);
                }
            }
        }

        // Detach the now-unused flat keys from per-company roles.
        $flatIds = DB::table('permissions')->whereIn('key', array_keys($this->map))->pluck('id');
        DB::table('role_permission')
            ->whereIn('role_id', $tenantRoleIds)
            ->whereIn('permission_id', $flatIds)
            ->delete();
    }

    public function down(): void
    {
        // One-way data fix; the flat keys are no longer used by the company area.
    }
};

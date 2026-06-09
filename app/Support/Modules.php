<?php

namespace App\Support;

use App\Models\Permission;

/**
 * Reads the module registry (config/modules.php) and turns it into permissions.
 *
 * Each module + action becomes a permission key "{module}.{action}". Adding a
 * module to the config makes it appear on the role screen automatically — and
 * ensure() creates any missing permission rows on the fly, so there is never a
 * manual "add permission" step.
 */
class Modules
{
    /** @return array<string,string> module key => label */
    public static function platform(): array
    {
        return config('modules.platform', []);
    }

    /** @return array<string,string> module key => label (company area) */
    public static function company(): array
    {
        return config('modules.company', []);
    }

    /** @return array<int,string> e.g. ['read','write','edit','delete'] */
    public static function actions(): array
    {
        return config('modules.actions', []);
    }

    public static function key(string $module, string $action): string
    {
        return "{$module}.{$action}";
    }

    /**
     * Make sure a permission row exists for every module/action, then return a
     * [key => id] map for building the checkbox grid.
     *
     * @return array<string,string>
     */
    public static function ensure(): array
    {
        $modules = self::platform() + self::company();

        foreach ($modules as $module => $label) {
            foreach (self::actions() as $action) {
                Permission::firstOrCreate(['key' => self::key($module, $action)]);
            }
        }

        return Permission::pluck('id', 'key')->all();
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Creates every permission listed in config/access.php ("projects.view", …),
 * the protected "Super Admin" role and the default roles (Editor, Viewer).
 * Safe to run again: existing permissions/roles are kept, defaults re-synced.
 */
class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $all = [];
        foreach (config('access.areas') as $area => $definition) {
            foreach ($definition['actions'] as $action) {
                $name = "{$area}.{$action}";
                Permission::findOrCreate($name, 'web');
                $all[] = $name;
            }
        }

        // Super Admin needs no permissions: Gate::before lets it do everything.
        Role::findOrCreate(config('access.super_admin'), 'web');

        foreach (config('access.default_roles') as $roleName => $patterns) {
            $role = Role::findOrCreate($roleName, 'web');
            $role->syncPermissions($this->expand($patterns, $all));
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /** ["projects.*", "messages.view"] → every matching permission name. */
    private function expand(array $patterns, array $all): array
    {
        return array_values(array_filter($all, function (string $name) use ($patterns) {
            foreach ($patterns as $pattern) {
                if (Str::is($pattern, $name)) {
                    return true;
                }
            }

            return false;
        }));
    }
}

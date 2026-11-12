<?php

namespace Tests;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /** Create the permissions and roles from config/access.php. */
    protected function seedPermissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    /** A dashboard user with one of the roles (Super Admin, Editor, Viewer…). */
    protected function userWithRole(string $role, array $attributes = []): User
    {
        $this->seedPermissions();
        $user = User::factory()->create($attributes);
        $user->assignRole($role);

        return $user;
    }

    protected function superAdmin(array $attributes = []): User
    {
        return $this->userWithRole(config('access.super_admin'), $attributes);
    }

    /** A dashboard user with exactly these permissions and no role. */
    protected function userWithPermissions(array $permissions): User
    {
        $this->seedPermissions();
        $user = User::factory()->create();
        $user->givePermissionTo($permissions);

        return $user;
    }
}

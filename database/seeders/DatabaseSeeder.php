<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * "php artisan db:seed" runs this. Every seeder is safe to run more than once.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,   // permissions + Super Admin / Editor / Viewer roles
            AdminUserSeeder::class,             // the first Super Admin account (from .env)
            ContentSeeder::class,               // services, projects, stack, numbers
        ]);

        // Sample websites and messages — never in production.
        if (app()->environment('local')) {
            $this->call(DemoSeeder::class);
        }
    }
}

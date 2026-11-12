<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Creates the first dashboard account (a Super Admin) from .env
 * (ADMIN_NAME, ADMIN_EMAIL, ADMIN_PASSWORD → config/access.php "first_admin").
 * If ADMIN_PASSWORD is empty a random password is generated and printed once.
 * An existing account with that email is left untouched (its password is
 * never overwritten).
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('access.first_admin.email');

        if (User::where('email', $email)->exists()) {
            $this->command?->info("Admin account {$email} already exists — left unchanged.");

            return;
        }

        $password = (string) config('access.first_admin.password');
        $generated = $password === '';
        if ($generated) {
            $password = Str::password(16, symbols: false);
        }

        $user = User::create([
            'name' => config('access.first_admin.name'),
            'email' => $email,
            'password' => $password,
            'is_active' => true,
        ]);
        $user->forceFill(['email_verified_at' => now()])->save();
        $user->assignRole(config('access.super_admin'));

        $this->command?->info("Super Admin created: {$email}");
        if ($generated) {
            $this->command?->warn("Generated password (shown only now — change it after signing in): {$password}");
        }
    }
}

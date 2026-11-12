<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/*
|--------------------------------------------------------------------------
| Rescue command: get back into the dashboard
|--------------------------------------------------------------------------
| php artisan rakiza:make-admin you@example.com --name="Your Name"
|
| Creates the account if it does not exist (or updates it if it does),
| switches it on, makes it a Super Admin and gives it a NEW random password,
| printed once. Use it when nobody can sign in any more.
*/
Artisan::command('rakiza:make-admin {email} {--name=}', function (string $email) {
    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $this->error('That is not a valid email address.');

        return 1;
    }

    // Make sure the roles exist (e.g. on a database that was never seeded).
    if (! Role::where('name', config('access.super_admin'))->exists()) {
        $this->call('db:seed', ['--class' => RolesAndPermissionsSeeder::class, '--force' => true]);
    }

    $password = Str::password(16, symbols: false);
    $user = User::firstOrNew(['email' => $email]);
    $isNew = ! $user->exists;

    $user->fill([
        'name' => $this->option('name') ?: ($user->name ?: Str::headline(Str::before($email, '@'))),
        'password' => $password,
        'is_active' => true,
    ]);
    $user->email_verified_at ??= now();
    $user->save();
    $user->syncRoles([config('access.super_admin')]);

    $this->info(($isNew ? 'Created' : 'Updated')." Super Admin: {$user->email}");
    $this->warn("New password (shown only now): {$password}");
    $this->line('Sign in at '.route('admin.login').' and change it under "My profile".');

    return 0;
})->purpose('Create or reset a Super Admin account (prints a new password)');

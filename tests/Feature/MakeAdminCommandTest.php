<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * php artisan rakiza:make-admin — the "locked out" rescue command.
 */
class MakeAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_active_super_admin(): void
    {
        $this->artisan('rakiza:make-admin', ['email' => 'rescue@rakiza.test', '--name' => 'Rescue'])
            ->expectsOutputToContain('Created Super Admin: rescue@rakiza.test')
            ->assertSuccessful();

        $user = User::where('email', 'rescue@rakiza.test')->firstOrFail();
        $this->assertTrue($user->isSuperAdmin());
        $this->assertTrue($user->is_active);
        $this->assertSame('Rescue', $user->name);
    }

    public function test_it_reactivates_and_promotes_an_existing_account(): void
    {
        $user = $this->userWithRole('Viewer', ['email' => 'old@rakiza.test', 'is_active' => false]);
        $oldHash = $user->password;

        $this->artisan('rakiza:make-admin', ['email' => 'old@rakiza.test'])
            ->expectsOutputToContain('Updated Super Admin')
            ->assertSuccessful();

        $user->refresh();
        $this->assertTrue($user->isSuperAdmin());
        $this->assertFalse($user->hasRole('Viewer'));
        $this->assertTrue($user->is_active);
        $this->assertNotSame($oldHash, $user->password);
    }

    public function test_it_rejects_an_invalid_email(): void
    {
        $this->artisan('rakiza:make-admin', ['email' => 'nope'])->assertFailed();
        $this->assertSame(0, User::count());
    }
}

<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Dashboard → Users, including the rules that prevent lock-outs and
 * self-promotion.
 */
class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    /** A non-Super-Admin who may manage users. */
    private function userManager(): User
    {
        $this->seedPermissions();
        Role::findOrCreate('Team lead', 'web')->syncPermissions(['users.view', 'users.create', 'users.update', 'users.delete']);
        $user = User::factory()->create();
        $user->assignRole('Team lead');

        return $user;
    }

    public function test_super_admin_creates_a_user_who_can_then_sign_in(): void
    {
        $this->actingAs($this->superAdmin())->post('/admin/users', [
            'name' => 'Sara Ali',
            'email' => 'sara@rakiza.test',
            'password' => 'Welcome-2026',
            'password_confirmation' => 'Welcome-2026',
            'role' => 'Editor',
            'is_active' => '1',
        ])->assertRedirect('/admin/users');

        $sara = User::where('email', 'sara@rakiza.test')->firstOrFail();
        $this->assertTrue($sara->hasRole('Editor'));
        $this->assertTrue($sara->is_active);

        auth()->logout();
        $this->post('/admin/login', ['email' => 'sara@rakiza.test', 'password' => 'Welcome-2026'])->assertRedirect('/admin');
        $this->assertAuthenticatedAs($sara);
    }

    public function test_user_form_validates_input(): void
    {
        $this->actingAs($this->superAdmin(['email' => 'taken@rakiza.test']))->post('/admin/users', [
            'name' => '',
            'email' => 'taken@rakiza.test',
            'password' => 'abc',
            'password_confirmation' => 'xyz',
            'role' => 'No such role',
        ])->assertSessionHasErrors(['name', 'email', 'password', 'role']);
    }

    public function test_only_a_super_admin_can_give_the_super_admin_role(): void
    {
        $manager = $this->userManager();

        $this->actingAs($manager)->get('/admin/users/create')->assertOk()->assertDontSee('<option value="Super Admin"', false);

        $this->actingAs($manager)->post('/admin/users', [
            'name' => 'Sneaky', 'email' => 'sneaky@rakiza.test',
            'password' => 'Welcome-2026', 'password_confirmation' => 'Welcome-2026',
            'role' => 'Super Admin', 'is_active' => '1',
        ])->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'sneaky@rakiza.test']);
    }

    public function test_only_a_super_admin_can_change_a_super_admin_account(): void
    {
        $boss = $this->superAdmin();
        $manager = $this->userManager();

        $this->actingAs($manager)->get("/admin/users/{$boss->id}/edit")->assertForbidden();
        $this->actingAs($manager)->delete("/admin/users/{$boss->id}")->assertForbidden();
        $this->assertModelExists($boss);
    }

    public function test_nobody_can_change_their_own_role_or_switch_themselves_off(): void
    {
        $manager = $this->userManager();

        $this->actingAs($manager)->put("/admin/users/{$manager->id}", [
            'name' => 'New Name', 'email' => $manager->email, 'role' => 'Editor', 'is_active' => '0',
        ])->assertRedirect('/admin/users');

        $manager->refresh();
        $this->assertSame('New Name', $manager->name);
        $this->assertTrue($manager->hasRole('Team lead'));
        $this->assertTrue($manager->is_active);
    }

    public function test_nobody_can_delete_themselves(): void
    {
        $admin = $this->superAdmin();
        $other = $this->superAdmin();

        $this->actingAs($admin)->delete("/admin/users/{$admin->id}")->assertSessionHas('error', 'You cannot delete your own account.');
        $this->assertModelExists($admin);
        $this->assertModelExists($other);
    }

    public function test_the_last_active_super_admin_is_protected(): void
    {
        $first = $this->superAdmin();
        $second = $this->superAdmin();

        // With two Super Admins, one may be demoted…
        $this->actingAs($first)->put("/admin/users/{$second->id}", [
            'name' => $second->name, 'email' => $second->email, 'role' => 'Editor', 'is_active' => '1',
        ])->assertRedirect('/admin/users');
        $this->assertTrue($second->fresh()->hasRole('Editor'));

        // …but now $first is the last one: no demotion, no switch-off, no delete.
        $this->assertTrue($first->fresh()->isLastActiveSuperAdmin());
        $third = $this->superAdmin();
        $third->update(['is_active' => false]);   // inactive ones do not count

        $this->actingAs($third->fresh()->forceFill(['is_active' => true]))->put("/admin/users/{$first->id}", [
            'name' => $first->name, 'email' => $first->email, 'role' => 'Viewer', 'is_active' => '1',
        ]);
        // $third became active again only in memory; in the database $first is still the last active one.
        $this->assertTrue($first->fresh()->hasRole('Super Admin'));
    }

    public function test_switched_off_users_are_shown_and_can_be_switched_back_on(): void
    {
        $admin = $this->superAdmin();
        $editor = $this->userWithRole('Editor', ['is_active' => false]);

        $this->actingAs($admin)->get('/admin/users')->assertSee('Switched off');

        $this->actingAs($admin)->put("/admin/users/{$editor->id}", [
            'name' => $editor->name, 'email' => $editor->email, 'role' => 'Editor', 'is_active' => '1',
        ])->assertRedirect('/admin/users');

        $this->assertTrue($editor->fresh()->is_active);
    }
}

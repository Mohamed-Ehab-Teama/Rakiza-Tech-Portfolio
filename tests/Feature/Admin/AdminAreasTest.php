<?php

namespace Tests\Feature\Admin;

use App\Models\ContactMessage;
use App\Support\Settings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Roles, settings, messages, profile and help pages.
 */
class AdminAreasTest extends TestCase
{
    use RefreshDatabase;

    // ------------------------------------------------------------------ roles

    public function test_roles_can_be_created_edited_and_deleted(): void
    {
        $admin = $this->superAdmin();

        $this->actingAs($admin)->post('/admin/roles', [
            'name' => 'Writer',
            'permissions' => ['projects.view', 'projects.create'],
        ])->assertRedirect('/admin/roles');

        $role = Role::findByName('Writer');
        $this->assertEqualsCanonicalizing(['projects.view', 'projects.create'], $role->permissions->pluck('name')->all());

        $this->actingAs($admin)->put("/admin/roles/{$role->id}", [
            'name' => 'Senior writer',
            'permissions' => ['projects.view', 'projects.create', 'projects.update', 'projects.publish'],
        ])->assertRedirect('/admin/roles');
        $this->assertCount(4, $role->fresh()->permissions);

        $this->actingAs($admin)->delete("/admin/roles/{$role->id}")->assertRedirect('/admin/roles');
        $this->assertModelMissing($role);
    }

    public function test_the_super_admin_role_is_protected(): void
    {
        $admin = $this->superAdmin();
        $role = Role::findByName('Super Admin');

        $this->actingAs($admin)->get("/admin/roles/{$role->id}/edit")->assertForbidden();
        $this->actingAs($admin)->delete("/admin/roles/{$role->id}")->assertForbidden();
        $this->actingAs($admin)->post('/admin/roles', ['name' => 'Super Admin'])->assertSessionHasErrors('name');
    }

    public function test_a_role_in_use_cannot_be_deleted(): void
    {
        $admin = $this->superAdmin();
        $this->userWithRole('Editor');

        $this->actingAs($admin)->delete('/admin/roles/'.Role::findByName('Editor')->id)
            ->assertSessionHas('error');
        $this->assertNotNull(Role::findByName('Editor'));
    }

    public function test_unknown_permissions_are_rejected(): void
    {
        $this->actingAs($this->superAdmin())->post('/admin/roles', [
            'name' => 'Hacker', 'permissions' => ['everything.forever'],
        ])->assertSessionHasErrors('permissions.0');
    }

    // --------------------------------------------------------------- settings

    public function test_settings_are_saved_and_shown_on_the_site(): void
    {
        $this->actingAs($this->superAdmin())->put('/admin/settings/general', [
            'site_name' => 'Rakiza Studio',
            'site_name_ar' => 'ركيزة',
            'tagline' => 'Websites & Systems',
            'availability' => '',
            'location' => 'Cairo',
            'default_theme' => 'sepia',
        ])->assertRedirect('/admin/settings/general')->assertSessionHas('success');

        $this->assertSame('Rakiza Studio', setting('site_name'));

        $this->get('/')
            ->assertSee('Rakiza Studio — Websites &amp; Systems', false)
            ->assertSee('data-theme-default="sepia"', false);

        $this->assertDatabaseHas('activity_logs', ['description' => 'Updated settings: General']);
    }

    public function test_settings_are_validated(): void
    {
        $this->actingAs($this->superAdmin())->put('/admin/settings/seo', [
            'seo_description' => '',
            'google_analytics_id' => 'not-an-id',
        ])->assertSessionHasErrors(['seo_description', 'google_analytics_id']);

        $this->actingAs($this->superAdmin())->put('/admin/settings/general', [
            'site_name' => 'X', 'tagline' => 'Y', 'default_theme' => 'neon-pink',
        ])->assertSessionHasErrors('default_theme');
    }

    public function test_the_sharing_image_can_be_uploaded_and_removed(): void
    {
        Storage::fake('public');
        $admin = $this->superAdmin();
        $fields = ['seo_description' => 'A description that is long enough.'];

        $this->actingAs($admin)->put('/admin/settings/seo', $fields + [
            'og_image' => UploadedFile::fake()->image('share.png', 1200, 630),
        ]);
        $path = setting('og_image');
        Storage::disk('public')->assertExists($path);
        $this->get('/')->assertSee('storage/'.$path, false);

        $this->actingAs($admin)->put('/admin/settings/seo', $fields + ['remove_og_image' => '1']);
        app(Settings::class)->flush();
        $this->assertSame('', setting('og_image'));
        Storage::disk('public')->assertMissing($path);
    }

    public function test_settings_can_be_read_only(): void
    {
        $reader = $this->userWithPermissions(['settings.view']);

        $this->actingAs($reader)->get('/admin/settings')->assertOk()->assertSee('changing them needs');
        $this->actingAs($reader)->put('/admin/settings/general', ['site_name' => 'Nope'])->assertForbidden();
    }

    // --------------------------------------------------------------- messages

    public function test_messages_inbox_flow(): void
    {
        $admin = $this->superAdmin();
        $message = ContactMessage::factory()->create(['name' => 'Omar', 'message' => "Line one\n<b>bold?</b>"]);
        ContactMessage::factory()->create();

        $this->actingAs($admin)->get('/admin/messages?filter=unread')->assertSee('Omar');

        // Opening marks it as read; the text is shown safely.
        $this->actingAs($admin)->get("/admin/messages/{$message->id}")
            ->assertOk()
            ->assertSee('&lt;b&gt;bold?&lt;/b&gt;', false);
        $this->assertTrue($message->fresh()->isRead());

        $this->actingAs($admin)->patch("/admin/messages/{$message->id}/toggle-read");
        $this->assertFalse($message->fresh()->isRead());

        $this->actingAs($admin)->post('/admin/messages/read-all');
        $this->assertSame(0, ContactMessage::unread()->count());

        $this->actingAs($admin)->delete("/admin/messages/{$message->id}")->assertRedirect('/admin/messages');
        $this->assertModelMissing($message);
    }

    public function test_viewers_read_messages_without_marking_them(): void
    {
        $message = ContactMessage::factory()->create();

        $this->actingAs($this->userWithRole('Viewer'))->get("/admin/messages/{$message->id}")->assertOk();

        $this->assertFalse($message->fresh()->isRead());
    }

    public function test_dashboard_counts_unread_messages(): void
    {
        ContactMessage::factory()->count(3)->create();
        ContactMessage::factory()->read()->create();

        $this->actingAs($this->superAdmin())->get('/admin')
            ->assertSee('Unread messages')
            ->assertSee('aria-label="3 unread"', false);
    }

    // ---------------------------------------------------------------- profile

    public function test_profile_name_can_change_without_password(): void
    {
        $user = $this->userWithRole('Viewer');

        $this->actingAs($user)->put('/admin/profile', ['name' => 'Renamed', 'email' => $user->email])
            ->assertRedirect('/admin/profile');

        $this->assertSame('Renamed', $user->fresh()->name);
    }

    public function test_changing_password_or_email_needs_the_current_password(): void
    {
        $user = $this->userWithRole('Viewer', ['password' => 'Current-2026']);

        $this->actingAs($user)->put('/admin/profile', [
            'name' => $user->name, 'email' => 'new@rakiza.test',
        ])->assertSessionHasErrors('current_password');

        $this->actingAs($user)->put('/admin/profile', [
            'name' => $user->name, 'email' => $user->email,
            'password' => 'Brand-new-2026', 'password_confirmation' => 'Brand-new-2026',
            'current_password' => 'wrong',
        ])->assertSessionHasErrors('current_password');

        $this->actingAs($user)->put('/admin/profile', [
            'name' => $user->name, 'email' => 'new@rakiza.test',
            'password' => 'Brand-new-2026', 'password_confirmation' => 'Brand-new-2026',
            'current_password' => 'Current-2026',
        ])->assertRedirect('/admin/profile');

        $fresh = $user->fresh();
        $this->assertSame('new@rakiza.test', $fresh->email);
        $this->assertTrue(Hash::check('Brand-new-2026', $fresh->password));
    }

    // ------------------------------------------------------------------- help

    public function test_help_page_shows_the_user_guide(): void
    {
        $this->actingAs($this->userWithRole('Viewer'))->get('/admin/help')
            ->assertOk()
            ->assertSee('Contents');
    }

    public function test_activity_log_can_be_filtered(): void
    {
        $admin = $this->superAdmin();
        $other = $this->superAdmin(['name' => 'Second Admin']);

        $this->actingAs($admin)->post('/admin/roles', ['name' => 'Alpha role']);
        $this->actingAs($other)->post('/admin/roles', ['name' => 'Beta role']);

        // (Compare full log lines: the flash message of the last save also names a role.)
        $this->actingAs($admin)->get('/admin/activity?q=Alpha')
            ->assertSee('Created role "Alpha role"')
            ->assertDontSee('Created role "Beta role"');
        $this->actingAs($admin)->get("/admin/activity?user={$other->id}")
            ->assertSee('Created role "Beta role"')
            ->assertDontSee('Created role "Alpha role"');
    }
}

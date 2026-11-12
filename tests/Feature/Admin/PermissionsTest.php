<?php

namespace Tests\Feature\Admin;

use App\Models\Project;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Who may do what: roles, permissions and the Super Admin bypass.
 */
class PermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_open_every_dashboard_page(): void
    {
        $admin = $this->superAdmin();
        $project = Project::factory()->create();

        foreach (['/admin', '/admin/projects', "/admin/projects/{$project->id}/edit", '/admin/websites', '/admin/services',
            '/admin/skill-categories', '/admin/skills', '/admin/metrics', '/admin/messages', '/admin/users', '/admin/roles',
            '/admin/settings', '/admin/settings/seo', '/admin/activity', '/admin/profile', '/admin/help'] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_viewer_can_look_but_not_change(): void
    {
        $viewer = $this->userWithRole('Viewer');
        $project = Project::factory()->create();

        $this->actingAs($viewer)->get('/admin/projects')
            ->assertOk()
            ->assertDontSee('New project')
            ->assertDontSee(route('admin.projects.edit', $project->id));

        $this->actingAs($viewer)->get('/admin/projects/create')->assertForbidden();
        $this->actingAs($viewer)->post('/admin/projects', ['title' => 'X', 'visual' => 'dashboard'])->assertForbidden();
        $this->actingAs($viewer)->get("/admin/projects/{$project->id}/edit")->assertForbidden();
        $this->actingAs($viewer)->delete("/admin/projects/{$project->id}")->assertForbidden();
        $this->actingAs($viewer)->patch("/admin/projects/{$project->id}/publish")->assertForbidden();
        $this->actingAs($viewer)->postJson('/admin/projects/reorder', ['ids' => [$project->id]])->assertForbidden();

        $this->assertDatabaseHas('projects', ['id' => $project->id]);
    }

    public function test_editor_manages_content_but_not_users_roles_or_settings(): void
    {
        $editor = $this->userWithRole('Editor');

        $this->actingAs($editor)->get('/admin/projects/create')->assertOk();
        $this->actingAs($editor)->get('/admin/messages')->assertOk();
        $this->actingAs($editor)->get('/admin/activity')->assertOk();

        $this->actingAs($editor)->get('/admin/users')->assertForbidden();
        $this->actingAs($editor)->get('/admin/roles')->assertForbidden();
        $this->actingAs($editor)->get('/admin/settings')->assertForbidden();
    }

    public function test_sidebar_only_shows_allowed_areas(): void
    {
        $this->actingAs($this->userWithRole('Viewer'))->get('/admin')
            ->assertSee('Projects')
            ->assertDontSee(route('admin.users.index'))
            ->assertDontSee(route('admin.settings.edit'));

        $this->actingAs($this->superAdmin())->get('/admin')
            ->assertSee(route('admin.users.index'))
            ->assertSee(route('admin.settings.edit'));
    }

    public function test_without_publish_permission_new_items_are_drafts(): void
    {
        $writer = $this->userWithPermissions(['websites.view', 'websites.create', 'websites.update']);

        $this->actingAs($writer)->get('/admin/websites/create')
            ->assertOk()
            ->assertDontSee('Published (visible to visitors)');

        $this->actingAs($writer)->post('/admin/websites', [
            'name' => 'Draft Site',
            'url' => 'https://draft.example',
            'status' => 'live',
            'is_published' => '1',       // ignored: no publish permission
        ])->assertRedirect('/admin/websites');

        $website = Website::where('name', 'Draft Site')->firstOrFail();
        $this->assertFalse($website->is_published);

        // Editing keeps the publish state as it was.
        $published = Website::factory()->create();
        $this->actingAs($writer)->put("/admin/websites/{$published->id}", [
            'name' => 'Renamed', 'url' => $published->url, 'status' => 'live', 'is_published' => '0',
        ]);
        $this->assertTrue($published->fresh()->is_published);
        $this->assertSame('Renamed', $published->fresh()->name);
    }

    public function test_publish_switch_needs_the_publish_permission(): void
    {
        $project = Project::factory()->create(['is_published' => true]);

        $this->actingAs($this->userWithPermissions(['projects.view', 'projects.update']))
            ->patch("/admin/projects/{$project->id}/publish")
            ->assertForbidden();

        $this->actingAs($this->userWithPermissions(['projects.view', 'projects.publish']))
            ->patch("/admin/projects/{$project->id}/publish")
            ->assertRedirect();

        $this->assertFalse($project->fresh()->is_published);
    }

    public function test_a_custom_permission_set_is_respected(): void
    {
        $user = $this->userWithPermissions(['messages.view']);

        $this->actingAs($user)->get('/admin/messages')->assertOk();
        $this->actingAs($user)->get('/admin/projects')->assertForbidden();
        $this->actingAs($user)->post('/admin/messages/read-all')->assertForbidden();
    }
}

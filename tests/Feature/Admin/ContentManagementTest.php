<?php

namespace Tests\Feature\Admin;

use App\Models\ActivityLog;
use App\Models\Metric;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use App\Models\SkillCategory;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Creating, editing, ordering and deleting content in the dashboard.
 */
class ContentManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin()
    {
        return $this->superAdmin();
    }

    public function test_project_can_be_created_with_a_cover_image(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->post('/admin/projects', [
            'title' => 'Clinic Booking',
            'slug' => '',
            'role' => 'Online appointments',
            'year' => '2026',
            'tags' => 'Laravel,  MySQL , ,Laravel',
            'summary' => 'Booking made easy.',
            'description' => '## Hello',
            'visual' => 'image',
            'cover_path' => UploadedFile::fake()->image('cover.jpg', 1200, 750),
            'is_featured' => '1',
            'is_published' => '1',
        ])->assertRedirect('/admin/projects')->assertSessionHas('success');

        $project = Project::where('title', 'Clinic Booking')->firstOrFail();
        $this->assertSame('clinic-booking', $project->slug);
        $this->assertSame(['Laravel', 'MySQL'], $project->tags);
        $this->assertSame(2026, $project->year);
        $this->assertTrue($project->is_published);
        Storage::disk('public')->assertExists($project->cover_path);
        $this->assertSame('image', $project->display_visual);

        $this->assertDatabaseHas('activity_logs', ['action' => 'created', 'description' => 'Created project "Clinic Booking"']);
    }

    public function test_replacing_and_removing_an_image_cleans_up_old_files(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $project = Project::factory()->create(['cover_path' => UploadedFile::fake()->image('old.jpg')->store('projects', 'public')]);
        $old = $project->cover_path;

        $payload = ['title' => $project->title, 'visual' => 'dashboard'];

        $this->actingAs($admin)->put("/admin/projects/{$project->id}", $payload + [
            'cover_path' => UploadedFile::fake()->image('new.png'),
        ]);
        $new = $project->fresh()->cover_path;
        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists($new);

        $this->actingAs($admin)->put("/admin/projects/{$project->id}", $payload + ['remove_cover_path' => '1']);
        $this->assertNull($project->fresh()->cover_path);
        Storage::disk('public')->assertMissing($new);
    }

    public function test_deleting_a_project_deletes_its_image(): void
    {
        Storage::fake('public');
        $project = Project::factory()->create(['cover_path' => UploadedFile::fake()->image('c.jpg')->store('projects', 'public')]);

        $this->actingAs($this->admin())->delete("/admin/projects/{$project->id}")
            ->assertRedirect('/admin/projects');

        $this->assertModelMissing($project);
        Storage::disk('public')->assertMissing($project->cover_path);
    }

    public function test_invalid_input_is_rejected(): void
    {
        $taken = Project::factory()->create(['title' => 'Taken', 'slug' => 'taken']);

        $this->actingAs($this->admin())->post('/admin/projects', [
            'title' => '',
            'slug' => 'taken',
            'visual' => 'unknown',
            'year' => '1800',
            'live_url' => 'not a url',
            'cover_path' => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf'),
        ])->assertSessionHasErrors(['title', 'slug', 'visual', 'year', 'live_url', 'cover_path']);

        $this->assertSame(1, Project::count());
        $this->assertNotNull($taken);
    }

    public function test_a_typed_slug_is_normalised(): void
    {
        $this->actingAs($this->admin())->post('/admin/projects', [
            'title' => 'Anything', 'slug' => 'My-Custom_Slug', 'visual' => 'map',
        ]);

        $this->assertDatabaseHas('projects', ['slug' => 'my-custom-slug']);
    }

    public function test_rows_can_be_reordered(): void
    {
        [$a, $b, $c] = Project::factory()->count(3)->create();

        $this->actingAs($this->admin())
            ->postJson('/admin/projects/reorder', ['ids' => [$c->id, $a->id, $b->id]])
            ->assertOk()
            ->assertJson(['message' => 'New order saved.']);

        $this->assertSame([$c->id, $a->id, $b->id], Project::ordered()->pluck('id')->all());
        $this->assertSame(1, ActivityLog::where('action', 'reordered')->count());

        $this->actingAs($this->admin())
            ->postJson('/admin/projects/reorder', ['ids' => [999, 999]])
            ->assertStatus(422);
    }

    public function test_publish_switch_toggles_visibility(): void
    {
        $website = Website::factory()->create(['is_published' => true]);
        $admin = $this->admin();

        $this->actingAs($admin)->patch("/admin/websites/{$website->id}/publish")->assertRedirect();
        $this->assertFalse($website->fresh()->is_published);

        $this->actingAs($admin)->patchJson("/admin/websites/{$website->id}/publish")
            ->assertOk()
            ->assertJson(['published' => true]);
    }

    public function test_list_search_and_status_filter(): void
    {
        Project::factory()->create(['title' => 'Alpha Portal']);
        Project::factory()->draft()->create(['title' => 'Beta Draft']);
        $admin = $this->admin();

        $this->actingAs($admin)->get('/admin/projects?q=alpha')->assertSee('Alpha Portal')->assertDontSee('Beta Draft');
        $this->actingAs($admin)->get('/admin/projects?status=draft')->assertSee('Beta Draft')->assertDontSee('Alpha Portal');
    }

    public function test_every_content_type_can_be_created_and_updated(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/websites', [
            'name' => 'Shop', 'url' => 'https://shop.example', 'status' => 'live', 'launched_on' => '2026-01-15', 'is_published' => '1',
        ])->assertRedirect('/admin/websites');
        $this->assertSame('2026-01-15', Website::first()->launched_on->format('Y-m-d'));

        $this->actingAs($admin)->post('/admin/services', [
            'title' => 'SEO', 'description' => 'Get found.', 'visual' => 'chart', 'tags' => 'Audit, Speed', 'is_published' => '1',
        ])->assertRedirect('/admin/services');
        $this->assertSame(['Audit', 'Speed'], Service::first()->tags);

        $this->actingAs($admin)->post('/admin/skill-categories', [
            'name' => 'Mobile', 'meta' => 'apps', 'icon' => 'mobile', 'is_published' => '1',
        ])->assertRedirect('/admin/skill-categories');
        $category = SkillCategory::first();

        $this->actingAs($admin)->post('/admin/skills', [
            'skill_category_id' => $category->id, 'name' => 'Flutter', 'level' => '85', 'marquee_row' => '2',
            'show_in_card' => '1', 'is_published' => '1',
        ])->assertRedirect('/admin/skills');
        $skill = Skill::first();
        $this->assertSame(85, $skill->level);
        $this->assertSame(2, $skill->marquee_row);

        $this->actingAs($admin)->post('/admin/metrics', [
            'group' => 'dashboard', 'label' => 'Uptime', 'value' => '99.95', 'decimals' => '2', 'suffix' => '%',
            'trend' => 'up', 'color' => 'term', 'is_published' => '1',
        ])->assertRedirect('/admin/metrics');
        $this->assertSame('99.95%', Metric::first()->formatted);

        // An edit that changes one field is logged with that field's name.
        $this->actingAs($admin)->put("/admin/skills/{$skill->id}", [
            'skill_category_id' => $category->id, 'name' => 'Flutter', 'level' => '', 'marquee_row' => '0', 'is_published' => '1',
        ])->assertRedirect('/admin/skills');
        $this->assertNull($skill->fresh()->level);
        $this->assertFalse($skill->fresh()->show_in_card);
        $this->assertContains('level', ActivityLog::where('action', 'updated')->latest('id')->first()->properties['changed']);
    }

    public function test_adding_a_skill_needs_a_category_first(): void
    {
        $this->actingAs($this->admin())->get('/admin/skills/create')
            ->assertRedirect('/admin/skill-categories/create')
            ->assertSessionHas('error');
    }

    public function test_deleting_a_category_deletes_its_skills(): void
    {
        $skill = Skill::factory()->create();

        $this->actingAs($this->admin())->delete("/admin/skill-categories/{$skill->skill_category_id}");

        $this->assertModelMissing($skill);
    }

    public function test_missing_records_return_404(): void
    {
        $this->actingAs($this->admin())->get('/admin/projects/12345/edit')->assertNotFound();
    }
}

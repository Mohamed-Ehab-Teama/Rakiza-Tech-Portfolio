<?php

namespace Tests\Feature;

use App\Models\Metric;
use App\Models\Project;
use App\Models\Setting;
use App\Models\Website;
use App\Support\Settings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Settings storage and small model behaviours used across the site.
 */
class SettingsAndModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_fall_back_to_their_defaults(): void
    {
        $this->assertSame('Rakiza Tech', setting('site_name'));
        $this->assertTrue(setting('notify_new_messages'));
        $this->assertCount(5, setting('hero_phrases'));
        $this->assertSame('fallback', setting('no_such_key', 'fallback'));
    }

    public function test_saved_settings_are_typed_and_cached_until_the_next_save(): void
    {
        $settings = app(Settings::class);
        $settings->set([
            'site_name' => 'Rakiza',
            'notify_new_messages' => false,
            'hero_phrases' => "one\n\n two \nthree",
            'not_a_setting' => 'ignored',
        ]);

        $this->assertSame('Rakiza', setting('site_name'));
        $this->assertFalse(setting('notify_new_messages'));
        $this->assertSame(['one', 'two', 'three'], setting('hero_phrases'));
        $this->assertDatabaseMissing('settings', ['key' => 'not_a_setting']);

        // Changing the table behind the cache's back is not seen until flush().
        Setting::where('key', 'site_name')->update(['value' => 'Changed']);
        $this->assertSame('Rakiza', setting('site_name'));
        $settings->flush();
        $this->assertSame('Changed', setting('site_name'));
    }

    public function test_theme_options_come_from_the_theme_list(): void
    {
        $options = app(Settings::class)->options('default_theme');

        $this->assertSame(['system', 'dark', 'light', 'dim', 'sepia', 'rakiza'], array_keys($options));
    }

    public function test_project_slugs_are_unique(): void
    {
        $first = Project::factory()->create(['title' => 'Booking App']);
        $second = Project::factory()->create(['title' => 'Booking App']);

        $this->assertSame('booking-app', $first->slug);
        $this->assertSame('booking-app-2', $second->slug);
    }

    public function test_new_records_go_to_the_end_of_the_order(): void
    {
        $a = Project::factory()->create();
        $b = Project::factory()->create();

        $this->assertGreaterThan($a->sort_order, $b->sort_order);
        $this->assertSame([$a->id, $b->id], Project::ordered()->pluck('id')->all());
    }

    public function test_metric_and_website_display_helpers(): void
    {
        $metric = Metric::factory()->make(['value' => 12400, 'decimals' => 0, 'prefix' => '$', 'suffix' => '+']);
        $this->assertSame('$12,400+', $metric->formatted);

        $website = Website::factory()->make(['url' => 'https://www.example.com/shop/', 'status' => 'in_progress']);
        $this->assertSame('example.com/shop', $website->display_url);
        $this->assertSame('In development', $website->status_label);
    }

    public function test_project_without_a_cover_falls_back_to_drawn_artwork(): void
    {
        $project = Project::factory()->make(['visual' => 'image', 'cover_path' => null]);
        $this->assertSame('dashboard', $project->display_visual);

        $project->cover_path = 'projects/cover.jpg';
        $this->assertSame('image', $project->display_visual);
        $this->assertStringEndsWith('/storage/projects/cover.jpg', $project->cover_url);
    }
}

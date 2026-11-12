<?php

namespace Tests\Feature;

use App\Models\Metric;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use App\Models\SkillCategory;
use App\Models\Website;
use App\Support\Settings;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The public website: home page, project pages, settings-driven texts.
 */
class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_shows_the_seeded_content(): void
    {
        $this->seed(ContentSeeder::class);

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Rakiza Tech — Digital Solutions: Websites, Systems &amp; Apps', false)
            ->assertSee('Garage Parking System')
            ->assertSee('What we build, end to end.')
            ->assertSee('Dashboards')
            ->assertSee('PostgreSQL')
            ->assertSee('Years experience')
            ->assertSee('12,400+')
            ->assertSee('id="terminal-data"', false)
            ->assertSee('application/ld+json', false);
    }

    public function test_home_page_works_with_an_empty_database(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('id="services"', false)
            ->assertDontSee('id="work"', false)
            ->assertDontSee('id="websites"', false)
            ->assertSee('id="contact"', false);
    }

    public function test_unpublished_records_are_not_shown(): void
    {
        Project::factory()->create(['title' => 'Public Project']);
        Project::factory()->draft()->create(['title' => 'Secret Draft']);
        Website::factory()->draft()->create(['name' => 'Hidden Website']);
        Service::factory()->create(['title' => 'Visible Service']);
        Service::factory()->create(['title' => 'Hidden Service', 'is_published' => false]);

        $this->get('/')
            ->assertSee('Public Project')
            ->assertDontSee('Secret Draft')
            ->assertDontSee('Hidden Website')
            ->assertSee('Visible Service')
            ->assertDontSee('Hidden Service');
    }

    public function test_websites_section_lists_published_websites(): void
    {
        Website::factory()->create(['name' => 'Nile Clinic', 'url' => 'https://www.nile-clinic.example/booking']);

        $this->get('/')
            ->assertSee('id="websites"', false)
            ->assertSee('Nile Clinic')
            ->assertSee('nile-clinic.example/booking')
            ->assertSee('https://www.nile-clinic.example/booking', false);
    }

    public function test_settings_change_the_texts_on_the_site(): void
    {
        app(Settings::class)->set([
            'hero_title' => 'We build foundations.',
            'contact_email' => 'hello@rakiza.example',
            'availability' => '',
        ]);

        $this->get('/')
            ->assertSee('We build foundations.')
            ->assertSee('mailto:hello@rakiza.example', false)
            ->assertDontSee('Available for new projects');
    }

    public function test_metrics_and_skills_render_from_the_database(): void
    {
        Metric::factory()->create(['label' => 'Cups of tea', 'short_label' => 'teas', 'value' => 321, 'suffix' => '+']);
        Metric::factory()->dashboard()->create(['label' => 'Deploys today', 'value' => 99.5, 'decimals' => 1, 'suffix' => '%']);
        $category = SkillCategory::factory()->create(['name' => 'Mobile']);
        Skill::factory()->for($category, 'category')->create(['name' => 'Flutter', 'level' => 80, 'marquee_row' => 2]);

        $this->get('/')
            ->assertSee('321+')
            ->assertSee('Cups of tea')
            ->assertSee('Deploys today')
            ->assertSee('99.5%')
            ->assertSee('Flutter')
            ->assertSee('--lvl:80%', false);
    }

    public function test_projects_index_lists_only_published_projects(): void
    {
        Project::factory()->create(['title' => 'Listed One']);
        Project::factory()->draft()->create(['title' => 'Not Listed']);

        $this->get('/projects')
            ->assertOk()
            ->assertSee('Listed One')
            ->assertDontSee('Not Listed');
    }

    public function test_project_page_uses_the_slug_and_renders_markdown_safely(): void
    {
        $project = Project::factory()->create([
            'title' => 'Fleet Tracker',
            'description' => "## What we built\n- Live map\n\n<script>alert('x')</script>",
            'live_url' => 'https://fleet.example',
        ]);

        $this->assertSame('fleet-tracker', $project->slug);

        $this->get('/projects/fleet-tracker')
            ->assertOk()
            ->assertSee('<h2>What we built</h2>', false)
            ->assertSee('<li>Live map</li>', false)
            ->assertDontSee("<script>alert('x')</script>", false)
            ->assertSee('https://fleet.example', false)
            ->assertSee('"@type":"CreativeWork"', false);
    }

    public function test_draft_project_is_hidden_from_visitors_but_previewable_by_staff(): void
    {
        $draft = Project::factory()->draft()->create(['title' => 'Coming Soon']);

        $this->get('/projects/'.$draft->slug)->assertNotFound();

        $viewer = $this->userWithRole('Viewer');
        $this->actingAs($viewer)
            ->get('/projects/'.$draft->slug)
            ->assertOk()
            ->assertSee('Draft preview');
    }

    public function test_unknown_project_returns_404(): void
    {
        $this->get('/projects/nothing-here')->assertNotFound()->assertSee('Page not found');
    }

    // -------------------------------------------------------------------- SEO

    public function test_pages_have_title_description_canonical_and_share_tags(): void
    {
        $project = Project::factory()->create(['title' => 'Fleet Tracker', 'summary' => 'Live vehicle tracking.']);

        $this->get('/')
            ->assertSee('<link rel="canonical" href="'.url('/').'"', false)
            ->assertSee('<meta property="og:image" content="'.asset('assets/brand/og-image.png').'"', false)
            ->assertSee('"@type":"Organization"', false);

        $this->get('/projects/'.$project->slug)
            ->assertSee('<title>Fleet Tracker — Rakiza Tech</title>', false)
            ->assertSee('<meta name="description" content="Live vehicle tracking." />', false)
            ->assertSee('<meta property="og:type" content="article" />', false)
            ->assertDontSee('noindex');
    }

    public function test_draft_previews_are_kept_out_of_search_engines(): void
    {
        $draft = Project::factory()->draft()->create();

        $this->actingAs($this->userWithRole('Viewer'))
            ->get('/projects/'.$draft->slug)
            ->assertSee('<meta name="robots" content="noindex, nofollow" />', false);
    }

    public function test_sitemap_lists_published_pages_only(): void
    {
        $public = Project::factory()->create(['title' => 'Public One']);
        $draft = Project::factory()->draft()->create(['title' => 'Hidden One']);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<loc>'.route('projects.show', $public).'</loc>', false)
            ->assertDontSee($draft->slug);
    }

    public function test_robots_txt_hides_the_dashboard_and_points_to_the_sitemap(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Disallow: /admin')
            ->assertSee('Sitemap: '.route('sitemap'));
    }
}

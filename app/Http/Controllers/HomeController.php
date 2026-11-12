<?php

namespace App\Http\Controllers;

use App\Models\Metric;
use App\Models\Project;
use App\Models\Service;
use App\Models\SkillCategory;
use App\Models\Website;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * The one-page landing site at "/". Loads everything the sections show;
 * only published records reach the page.
 */
class HomeController extends Controller
{
    public function __invoke(): View
    {
        $categories = SkillCategory::published()->ordered()
            ->with(['skills' => fn ($query) => $query->published()])
            ->get();

        $projects = Project::published()->featured()->ordered()->get();
        $services = Service::published()->ordered()->get();

        return view('home', [
            'services' => $services,
            'projects' => $projects,
            'projectCount' => Project::published()->count(),
            'websites' => Website::published()->ordered()->get(),
            'categories' => $categories,
            'marqueeRows' => $this->marqueeRows($categories),
            'proficiency' => $this->proficiency($categories),
            'highlights' => Metric::published()->group('highlight')->ordered()->get(),
            'kpis' => Metric::published()->group('dashboard')->ordered()->get(),
            'terminal' => $this->terminalData($projects, $services, $categories),
        ]);
    }

    /** The two scrolling rows of skill names (empty rows are dropped). */
    private function marqueeRows(Collection $categories): array
    {
        $skills = $categories->flatMap->skills;

        return array_values(array_filter([
            $skills->where('marquee_row', 1)->pluck('name')->all(),
            $skills->where('marquee_row', 2)->pluck('name')->all(),
        ]));
    }

    /**
     * The "Proficiency" donuts: the average level of the first two categories
     * that have skills with a level.
     */
    private function proficiency(Collection $categories): Collection
    {
        return $categories
            ->map(fn (SkillCategory $category) => [
                'name' => $category->name,
                'value' => (int) round($category->skills->whereNotNull('level')->avg('level') ?? 0),
            ])
            ->filter(fn ($item) => $item['value'] > 0)
            ->take(2)
            ->values();
    }

    /** Everything the interactive terminal answers with (read by terminal.js). */
    private function terminalData(Collection $projects, Collection $services, Collection $categories): array
    {
        return [
            'name' => setting('site_name'),
            'tagline' => setting('tagline'),
            'location' => setting('location'),
            'availability' => setting('availability'),
            'email' => setting('contact_email'),
            'phone' => setting('contact_phone'),
            'socials' => social_links(),
            'projects' => $projects->map(fn (Project $p) => [
                'title' => $p->title,
                'stack' => implode(' · ', $p->tags ?? []),
                'year' => $p->year ? (string) $p->year : '',
                'url' => route('projects.show', $p),
            ])->values(),
            'services' => $services->map(fn (Service $s) => [
                'name' => $s->title,
                'description' => $s->description,
            ])->values(),
            'skills' => $categories->map(fn (SkillCategory $c) => [
                'group' => $c->name,
                'items' => $c->skills->where('show_in_card', true)->pluck('name')->values(),
            ])->filter(fn ($group) => $group['items']->isNotEmpty())->values(),
        ];
    }
}

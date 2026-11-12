<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Response;

/**
 * Files for search engines:
 *   /sitemap.xml  — every public page, so Google finds them all
 *   /robots.txt   — where crawlers may go (not /admin) and where the sitemap is
 */
class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $projects = Project::published()->ordered()->get(['slug', 'updated_at']);
        $latest = $projects->max('updated_at');

        $urls = [
            ['loc' => route('home'), 'lastmod' => $latest, 'priority' => '1.0'],
            ['loc' => route('projects.index'), 'lastmod' => $latest, 'priority' => '0.8'],
        ];
        foreach ($projects as $project) {
            $urls[] = ['loc' => route('projects.show', $project), 'lastmod' => $project->updated_at, 'priority' => '0.7'];
        }

        return response()
            ->view('seo.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines)."\n")
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}

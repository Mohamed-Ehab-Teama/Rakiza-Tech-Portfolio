<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Public project pages: /projects (all) and /projects/{slug} (one).
 */
class ProjectController extends Controller
{
    public function index(): View
    {
        return view('projects.index', [
            'projects' => Project::published()->ordered()->get(),
        ]);
    }

    public function show(Request $request, Project $project): View
    {
        // Drafts are hidden from visitors, but dashboard users who can see
        // projects get a preview (with a "draft" banner).
        $isPreview = ! $project->is_published;
        abort_if($isPreview && ! $request->user()?->can('projects.view'), 404);

        $published = Project::published()->ordered()->get(['id', 'title', 'slug']);
        $index = $published->search(fn (Project $p) => $p->is($project));

        return view('projects.show', [
            'project' => $project,
            'isPreview' => $isPreview,
            'previous' => $index !== false && $index > 0 ? $published[$index - 1] : null,
            'next' => $index !== false ? $published->get($index + 1) : null,
        ]);
    }
}

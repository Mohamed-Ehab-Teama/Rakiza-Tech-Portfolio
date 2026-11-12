<?php

namespace App\Http\Controllers\Admin;

use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Dashboard → Projects. Everything (list, form, saving, permissions) comes
 * from ResourceController; this class only describes projects.
 */
class ProjectController extends ResourceController
{
    protected static string $area = 'projects';

    protected function model(): string
    {
        return Project::class;
    }

    protected function labels(): array
    {
        return [
            'singular' => 'Project',
            'plural' => 'Projects',
            'route' => 'projects',
            'description' => 'Case studies shown in "Selected work" on the home page and on /projects.',
        ];
    }

    protected function searchable(): array
    {
        return ['title', 'role', 'client'];
    }

    protected function columns(): array
    {
        return [
            'Title' => 'title',
            'Subtitle' => 'role',
            'Year' => 'year',
            'Home page' => fn (Project $project) => $project->is_featured ? 'Featured' : 'Hidden',
        ];
    }

    protected function fields(?Model $record): array
    {
        return [
            'title' => [
                'section' => 'Project', 'label' => 'Title', 'type' => 'text', 'width' => 'half',
                'rules' => ['required', 'string', 'max:120'],
            ],
            'slug' => [
                'section' => 'Project', 'label' => 'URL slug', 'type' => 'text', 'width' => 'half',
                'rules' => ['nullable', 'string', 'max:140', 'alpha_dash', Rule::unique('projects', 'slug')->ignore($record?->getKey())],
                'help' => 'The end of the page address (/projects/your-slug). Leave empty to make one from the title.',
            ],
            'role' => [
                'section' => 'Project', 'label' => 'Subtitle', 'type' => 'text', 'width' => 'half',
                'rules' => ['nullable', 'string', 'max:120'],
                'help' => 'One line under the title, e.g. "Parking management platform".',
            ],
            'client' => [
                'section' => 'Project', 'label' => 'Client', 'type' => 'text', 'width' => 'quarter',
                'rules' => ['nullable', 'string', 'max:120'],
            ],
            'year' => [
                'section' => 'Project', 'label' => 'Year', 'type' => 'number', 'width' => 'quarter',
                'rules' => ['nullable', 'integer', 'between:1990,2100'],
            ],
            'tags' => [
                'section' => 'Project', 'label' => 'Tech stack', 'type' => 'tags',
                'rules' => ['nullable', 'string', 'max:300'],
                'help' => 'Separate with commas, e.g. "Laravel, MySQL, Tailwind". The first one is highlighted.',
            ],
            'summary' => [
                'section' => 'Write-up', 'label' => 'Summary', 'type' => 'textarea',
                'rules' => ['nullable', 'string', 'max:500'],
                'help' => 'Two or three sentences. Shown at the top of the project page and in Google results.',
            ],
            'description' => [
                'section' => 'Write-up', 'label' => 'Full write-up', 'type' => 'markdown',
                'rules' => ['nullable', 'string', 'max:20000'],
                'help' => 'Write "## " before a heading and "- " before list items. Leave an empty line between paragraphs.',
            ],
            'visual' => [
                'section' => 'Look', 'label' => 'Card artwork', 'type' => 'select', 'width' => 'half',
                'options' => Project::VISUALS,
                'rules' => ['required', Rule::in(array_keys(Project::VISUALS))],
                'help' => 'The drawing at the top of the card. "Uploaded image" shows the cover below.',
            ],
            'cover_path' => [
                'section' => 'Look', 'label' => 'Cover image', 'type' => 'image', 'width' => 'half',
                'directory' => 'projects',
                'rules' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
                'help' => 'A screenshot (JPG, PNG or WebP, max 4 MB). Also used when the page is shared.',
            ],
            'live_url' => [
                'section' => 'Links', 'label' => 'Live site', 'type' => 'url', 'width' => 'half',
                'rules' => ['nullable', 'url', 'max:255'],
            ],
            'repo_url' => [
                'section' => 'Links', 'label' => 'Source code', 'type' => 'url', 'width' => 'half',
                'rules' => ['nullable', 'url', 'max:255'],
            ],
            'seo_title' => [
                'section' => 'Search engines (optional)', 'label' => 'SEO title', 'type' => 'text',
                'rules' => ['nullable', 'string', 'max:70'],
                'help' => 'Empty = the project title.',
            ],
            'seo_description' => [
                'section' => 'Search engines (optional)', 'label' => 'SEO description', 'type' => 'textarea',
                'rules' => ['nullable', 'string', 'max:300'],
                'help' => 'Empty = the summary.',
            ],
            'is_featured' => [
                'section' => 'Visibility', 'label' => 'Show on the home page', 'type' => 'boolean', 'default' => true,
            ],
            'is_published' => [
                'section' => 'Visibility', 'label' => 'Published (visible to visitors)', 'type' => 'boolean',
                'publish' => true, 'default' => true,
            ],
        ];
    }

    protected function beforeSave(array $data, ?Model $record): array
    {
        // Normalise a typed slug ("My-Project" → "my-project"); empty = made from the title.
        $data['slug'] = filled($data['slug'] ?? null) ? Str::slug($data['slug']) : null;

        return $data;
    }

    protected function publicUrl(Model $record): ?string
    {
        return route('projects.show', $record);
    }
}

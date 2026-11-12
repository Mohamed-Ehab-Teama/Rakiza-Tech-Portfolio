<?php

namespace App\Http\Controllers\Admin;

use App\Models\SkillCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

/**
 * Dashboard → Skills → Categories (the cards of the "Stack" section).
 * Uses the "skills" permissions.
 */
class SkillCategoryController extends ResourceController
{
    protected static string $area = 'skills';

    protected function model(): string
    {
        return SkillCategory::class;
    }

    protected function labels(): array
    {
        return [
            'singular' => 'Skill category',
            'plural' => 'Skill categories',
            'route' => 'skill_categories',
            'description' => 'The cards of the "Stack" section (Frontend, Backend…). Their skills are managed under Skills.',
        ];
    }

    protected function listQuery(): Builder
    {
        return SkillCategory::query()->withCount('skills')->ordered();
    }

    protected function searchable(): array
    {
        return ['name', 'meta'];
    }

    protected function columns(): array
    {
        return [
            'Name' => 'name',
            'Small label' => 'meta',
            'Icon' => fn (SkillCategory $category) => SkillCategory::ICONS[$category->icon] ?? $category->icon,
            'Skills' => 'skills_count',
        ];
    }

    protected function fields(?Model $record): array
    {
        return [
            'name' => [
                'section' => 'Category', 'label' => 'Name', 'type' => 'text', 'width' => 'half',
                'rules' => ['required', 'string', 'max:60'],
            ],
            'meta' => [
                'section' => 'Category', 'label' => 'Small label', 'type' => 'text', 'width' => 'half',
                'rules' => ['nullable', 'string', 'max:40'],
                'help' => 'Shown in the card corner, e.g. "UI / UX".',
            ],
            'icon' => [
                'section' => 'Category', 'label' => 'Icon', 'type' => 'select', 'width' => 'half',
                'options' => SkillCategory::ICONS,
                'rules' => ['required', Rule::in(array_keys(SkillCategory::ICONS))],
            ],
            'is_published' => [
                'section' => 'Visibility', 'label' => 'Published (visible to visitors)', 'type' => 'boolean',
                'publish' => true, 'default' => true,
            ],
        ];
    }
}

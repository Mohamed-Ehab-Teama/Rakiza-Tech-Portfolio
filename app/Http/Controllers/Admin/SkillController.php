<?php

namespace App\Http\Controllers\Admin;

use App\Models\Skill;
use App\Models\SkillCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Dashboard → Skills: technologies inside the "Stack" categories, the
 * proficiency bars and the two scrolling rows. Uses the "skills" permissions.
 */
class SkillController extends ResourceController
{
    protected static string $area = 'skills';

    protected function model(): string
    {
        return Skill::class;
    }

    protected function labels(): array
    {
        return [
            'singular' => 'Skill',
            'plural' => 'Skills',
            'route' => 'skills',
            'description' => 'Technologies in the "Stack" section. A level adds a progress bar; the scrolling row decides where the name runs across the screen.',
        ];
    }

    /** Listed by category (in the categories' order), then by their own order. */
    protected function listQuery(): Builder
    {
        return Skill::query()
            ->with('category')
            ->orderBy(
                SkillCategory::select('sort_order')->whereColumn('skill_categories.id', 'skills.skill_category_id')
            )
            ->ordered();
    }

    protected function searchable(): array
    {
        return ['name'];
    }

    protected function columns(): array
    {
        return [
            'Name' => 'name',
            'Category' => fn (Skill $skill) => $skill->category?->name,
            'Level' => fn (Skill $skill) => $skill->level !== null ? $skill->level.'%' : '—',
            'In card' => fn (Skill $skill) => $skill->show_in_card ? 'Yes' : 'No',
            'Scrolling row' => fn (Skill $skill) => [0 => '—', 1 => 'Top', 2 => 'Bottom'][$skill->marquee_row] ?? '—',
        ];
    }

    protected function fields(?Model $record): array
    {
        return [
            'skill_category_id' => [
                'section' => 'Skill', 'label' => 'Category', 'type' => 'select', 'width' => 'half',
                'options' => SkillCategory::ordered()->pluck('name', 'id')->all(),
                'rules' => ['required', 'integer', 'exists:skill_categories,id'],
            ],
            'name' => [
                'section' => 'Skill', 'label' => 'Name', 'type' => 'text', 'width' => 'half',
                'rules' => ['required', 'string', 'max:60'],
            ],
            'level' => [
                'section' => 'Where it appears', 'label' => 'Level (0–100)', 'type' => 'number', 'width' => 'half',
                'rules' => ['nullable', 'integer', 'between:0,100'],
                'help' => 'Fill in to show a progress bar in the category card. Leave empty for no bar.',
            ],
            'marquee_row' => [
                'section' => 'Where it appears', 'label' => 'Scrolling row', 'type' => 'select', 'width' => 'half',
                'options' => Skill::MARQUEE_ROWS, 'default' => 1,
                'rules' => ['required', 'integer', Rule::in(array_keys(Skill::MARQUEE_ROWS))],
            ],
            'show_in_card' => [
                'section' => 'Where it appears', 'label' => 'Show as a tag in its category card', 'type' => 'boolean', 'default' => true,
            ],
            'is_published' => [
                'section' => 'Visibility', 'label' => 'Published (visible to visitors)', 'type' => 'boolean',
                'publish' => true, 'default' => true,
            ],
        ];
    }

    /** Adding a skill needs a category to put it in. */
    public function create(): View|RedirectResponse
    {
        if (! SkillCategory::exists()) {
            return redirect()->route('admin.skill_categories.create')
                ->with('error', 'Create a skill category first — every skill belongs to one.');
        }

        return parent::create();
    }
}

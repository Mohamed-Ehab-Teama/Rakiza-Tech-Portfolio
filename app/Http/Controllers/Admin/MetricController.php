<?php

namespace App\Http\Controllers\Admin;

use App\Models\Metric;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

/**
 * Dashboard → Numbers: the highlights under the hero / in About, and the
 * KPI cards of the "By the numbers" dashboard.
 */
class MetricController extends ResourceController
{
    protected static string $area = 'metrics';

    protected function model(): string
    {
        return Metric::class;
    }

    protected function labels(): array
    {
        return [
            'singular' => 'Number',
            'plural' => 'Numbers',
            'route' => 'metrics',
            'description' => 'Highlights appear under the hero and in About; dashboard cards appear in "By the numbers".',
        ];
    }

    /** Grouped (highlights first), then in their own order. */
    protected function listQuery(): Builder
    {
        return Metric::query()->orderByRaw("CASE WHEN `group` = 'highlight' THEN 0 ELSE 1 END")->ordered();
    }

    protected function searchable(): array
    {
        return ['label', 'short_label', 'note'];
    }

    protected function columns(): array
    {
        return [
            'Label' => 'label',
            'Shown as' => 'formatted',
            'Where' => fn (Metric $metric) => $metric->group === 'highlight' ? 'Highlights' : 'Dashboard card',
            'Note' => 'note',
        ];
    }

    protected function fields(?Model $record): array
    {
        return [
            'group' => [
                'section' => 'Number', 'label' => 'Where it appears', 'type' => 'select',
                'options' => Metric::GROUPS,
                'rules' => ['required', Rule::in(array_keys(Metric::GROUPS))],
            ],
            'label' => [
                'section' => 'Number', 'label' => 'Label', 'type' => 'text', 'width' => 'half',
                'rules' => ['required', 'string', 'max:60'],
                'help' => 'E.g. "Years experience" or "Deploys".',
            ],
            'short_label' => [
                'section' => 'Number', 'label' => 'Short label', 'type' => 'text', 'width' => 'half',
                'rules' => ['nullable', 'string', 'max:30'],
                'help' => 'Highlights only: the one-word label under the hero, e.g. "years".',
            ],
            'value' => [
                'section' => 'Value', 'label' => 'Number', 'type' => 'number', 'width' => 'quarter',
                'rules' => ['required', 'numeric', 'between:-999999999,999999999'],
                'step' => 'any',
            ],
            'decimals' => [
                'section' => 'Value', 'label' => 'Decimals', 'type' => 'select', 'width' => 'quarter',
                'options' => [0 => '0 (1,240)', 1 => '1 (99.5)', 2 => '2 (99.98)'],
                'rules' => ['required', 'integer', 'between:0,2'],
            ],
            'prefix' => [
                'section' => 'Value', 'label' => 'Before the number', 'type' => 'text', 'width' => 'quarter',
                'rules' => ['nullable', 'string', 'max:10'],
                'help' => 'E.g. "$".',
            ],
            'suffix' => [
                'section' => 'Value', 'label' => 'After the number', 'type' => 'text', 'width' => 'quarter',
                'rules' => ['nullable', 'string', 'max:10'],
                'help' => 'E.g. "+" or "%".',
            ],
            'badge' => [
                'section' => 'Dashboard card details', 'label' => 'Corner tag', 'type' => 'text', 'width' => 'quarter',
                'rules' => ['nullable', 'string', 'max:20'],
                'help' => 'E.g. "YTD" or "30d".',
            ],
            'note' => [
                'section' => 'Dashboard card details', 'label' => 'Note under the number', 'type' => 'text', 'width' => 'half',
                'rules' => ['nullable', 'string', 'max:120'],
                'help' => 'E.g. "+12.4% this month".',
            ],
            'trend' => [
                'section' => 'Dashboard card details', 'label' => 'Trend arrow', 'type' => 'select', 'width' => 'quarter',
                'options' => Metric::TRENDS,
                'rules' => ['required', Rule::in(array_keys(Metric::TRENDS))],
            ],
            'color' => [
                'section' => 'Dashboard card details', 'label' => 'Line color', 'type' => 'select', 'width' => 'quarter',
                'options' => Metric::COLORS,
                'rules' => ['required', Rule::in(array_keys(Metric::COLORS))],
            ],
            'is_published' => [
                'section' => 'Visibility', 'label' => 'Published (visible to visitors)', 'type' => 'boolean',
                'publish' => true, 'default' => true,
            ],
        ];
    }
}

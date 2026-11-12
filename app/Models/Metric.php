<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A number on the site: hero/About highlights or a dashboard KPI card.
 */
#[Fillable([
    'group', 'label', 'short_label', 'value', 'decimals', 'prefix', 'suffix',
    'badge', 'note', 'trend', 'color', 'is_published', 'sort_order',
])]
class Metric extends Model
{
    use HasFactory, Publishable, RecordsActivity, Sortable;

    public const GROUPS = [
        'highlight' => 'Highlights (under the hero and in About)',
        'dashboard' => 'Dashboard cards ("By the numbers")',
    ];

    public const TRENDS = ['up' => 'Going up', 'down' => 'Going down', 'flat' => 'Steady'];

    public const COLORS = ['accent' => 'Accent', 'term' => 'Green', 'violet' => 'Violet', 'amber' => 'Amber'];

    protected function casts(): array
    {
        return [
            'value' => 'float',
            'decimals' => 'integer',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeGroup(Builder $query, string $group): Builder
    {
        return $query->where('group', $group);
    }

    /** The number as shown, e.g. "12,400+" or "99.98%". */
    protected function formatted(): Attribute
    {
        return Attribute::get(fn () => $this->prefix.number_format($this->value, $this->decimals).$this->suffix);
    }
}

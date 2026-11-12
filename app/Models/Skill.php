<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One technology/skill. Where it shows up:
 *  - as a tag in its category card    (show_in_card)
 *  - as a proficiency bar             (level is set, 0–100)
 *  - in a scrolling marquee row       (marquee_row: 0 none, 1 top, 2 bottom)
 */
#[Fillable(['skill_category_id', 'name', 'level', 'show_in_card', 'marquee_row', 'is_published', 'sort_order'])]
class Skill extends Model
{
    use HasFactory, Publishable, RecordsActivity, Sortable;

    public const MARQUEE_ROWS = [
        0 => 'Not in the scrolling rows',
        1 => 'Top scrolling row',
        2 => 'Bottom scrolling row',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'show_in_card' => 'boolean',
            'marquee_row' => 'integer',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SkillCategory::class, 'skill_category_id');
    }
}

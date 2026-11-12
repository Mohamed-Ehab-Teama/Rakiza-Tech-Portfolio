<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A card in the "Stack" section (Frontend, Backend…), holding skills.
 */
#[Fillable(['name', 'meta', 'icon', 'is_published', 'sort_order'])]
class SkillCategory extends Model
{
    use HasFactory, Publishable, RecordsActivity, Sortable;

    public const ICONS = [
        'frontend' => 'Screen (frontend)',
        'backend' => 'Brackets (backend)',
        'database' => 'Cylinder (database)',
        'devops' => 'Gear (DevOps & tools)',
        'design' => 'Pen (design)',
        'mobile' => 'Phone (mobile)',
        'cloud' => 'Cloud',
        'other' => 'Star (other)',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class)->orderBy('sort_order')->orderBy('id');
    }
}

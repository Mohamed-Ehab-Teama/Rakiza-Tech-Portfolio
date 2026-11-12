<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A service card in "What we build".
 */
#[Fillable(['title', 'description', 'visual', 'tags', 'is_wide', 'is_published', 'sort_order'])]
class Service extends Model
{
    use HasFactory, Publishable, RecordsActivity, Sortable;

    /** Icon + small animation shown on the card. */
    public const VISUALS = [
        'code' => 'Code editor',
        'app' => 'Phone app',
        'chart' => 'Bar chart',
        'db' => 'Database table',
        'cli' => 'Command line',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'is_wide' => 'boolean',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}

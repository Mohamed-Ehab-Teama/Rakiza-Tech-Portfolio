<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Records that the dashboard can re-order by drag & drop (sort_order column).
 * New records go to the end of the list; ->ordered() returns them in order.
 */
trait Sortable
{
    public static function bootSortable(): void
    {
        static::creating(function ($model) {
            if (! $model->sort_order) {
                $model->sort_order = (int) static::query()->max('sort_order') + 1;
            }
        });
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}

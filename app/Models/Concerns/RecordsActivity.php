<?php

namespace App\Models\Concerns;

use App\Models\ActivityLog;
use Illuminate\Support\Arr;

/**
 * Writes an entry to the activity log whenever a dashboard user creates,
 * updates or deletes this record (nothing is logged for visitors or seeders,
 * because there is no signed-in user then).
 */
trait RecordsActivity
{
    public static function bootRecordsActivity(): void
    {
        static::created(fn ($model) => ActivityLog::record('created', $model));

        static::updated(function ($model) {
            $changed = array_keys(Arr::except($model->getChanges(), ['updated_at']));
            ActivityLog::record('updated', $model, $changed);
        });

        static::deleted(fn ($model) => ActivityLog::record('deleted', $model));
    }

    /** The name shown in the activity log, e.g. the project title. */
    public function activityLabel(): string
    {
        return (string) ($this->title ?? $this->name ?? $this->label ?? ('#'.$this->getKey()));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

/**
 * One line of the dashboard's activity log: who did what, and when.
 */
#[Fillable(['user_id', 'action', 'subject_type', 'subject_id', 'description', 'properties'])]
class ActivityLog extends Model
{
    protected function casts(): array
    {
        return ['properties' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Log an action by the signed-in user. Does nothing without a signed-in
     * user, or for an "updated" event that changed nothing worth showing.
     *
     * @param  array<int, string>  $changed  names of the fields that changed
     */
    public static function record(string $action, ?Model $subject = null, array $changed = [], ?string $description = null): ?self
    {
        if (! auth()->check()) {
            return null;
        }
        if ($action === 'updated' && $changed === []) {
            return null;
        }

        if ($description === null && $subject !== null) {
            $label = method_exists($subject, 'activityLabel') ? $subject->activityLabel() : '#'.$subject->getKey();
            $description = Str::ucfirst($action).' '.static::typeName($subject).' "'.Str::limit($label, 120).'"';
        }

        return static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'description' => Str::limit($description ?? Str::ucfirst($action), 250),
            'properties' => $changed ? ['changed' => $changed] : null,
        ]);
    }

    /** "SkillCategory" → "skill category". */
    public static function typeName(Model $model): string
    {
        return Str::lower(Str::headline(class_basename($model)));
    }
}

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
use Illuminate\Support\Str;

/**
 * A portfolio project ("Selected work" on the home page, /projects/{slug}).
 */
#[Fillable([
    'title', 'slug', 'role', 'client', 'year', 'summary', 'description', 'tags', 'visual',
    'cover_path', 'live_url', 'repo_url', 'seo_title', 'seo_description',
    'is_featured', 'is_published', 'sort_order',
])]
class Project extends Model
{
    use HasFactory, Publishable, RecordsActivity, Sortable;

    /** Card artwork choices (shown in the dashboard form). "image" uses the uploaded cover. */
    public const VISUALS = [
        'dashboard' => 'Analytics dashboard',
        'store' => 'Course / store page',
        'kanban' => 'Task board',
        'terminal' => 'Terminal window',
        'map' => 'Live map',
        'image' => 'Uploaded image (cover)',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'year' => 'integer',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        // Every project needs a unique slug for its URL; make one from the title.
        static::saving(function (Project $project) {
            if (blank($project->slug)) {
                $project->slug = static::uniqueSlug($project->title, $project->id);
            }
        });
    }

    /** "My Project" → "my-project" (or "my-project-2" if taken). */
    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'project';
        $slug = $base;
        $i = 2;
        while (static::where('slug', $slug)->when($ignoreId, fn (Builder $q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /** Public URL of the uploaded cover image, or null. */
    protected function coverUrl(): Attribute
    {
        return Attribute::get(fn () => $this->cover_path ? asset('storage/'.$this->cover_path) : null);
    }

    /** The artwork to draw: the cover only when one is uploaded. */
    protected function displayVisual(): Attribute
    {
        return Attribute::get(function () {
            if ($this->visual === 'image') {
                return $this->cover_path ? 'image' : 'dashboard';
            }

            return array_key_exists($this->visual, self::VISUALS) ? $this->visual : 'dashboard';
        });
    }
}

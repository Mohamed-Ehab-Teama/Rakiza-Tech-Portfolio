<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

/**
 * Reads and saves site settings.
 *
 * Definitions (labels, types, defaults, rules) come from config/settings.php.
 * Saved values live in the `settings` table and are cached until the next save.
 * Use the helper: setting('site_name').
 */
class Settings
{
    private const CACHE_KEY = 'site.settings';

    /** @var array<string, string|null>|null saved values for this request */
    private ?array $saved = null;

    /** All field definitions keyed by setting key (groups flattened). */
    public function definitions(): array
    {
        $fields = [];
        foreach (config('settings.groups', []) as $group => $definition) {
            foreach ($definition['fields'] as $key => $field) {
                $fields[$key] = $field + ['group' => $group];
            }
        }

        return $fields;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->definitions());
    }

    /** A setting's value (typed by its field type), or its default. */
    public function get(string $key, mixed $default = null): mixed
    {
        $field = $this->definitions()[$key] ?? null;
        $saved = $this->saved();

        $raw = array_key_exists($key, $saved) ? $saved[$key] : ($field['default'] ?? $default);

        return $this->cast($field['type'] ?? 'text', $raw);
    }

    /**
     * Save several settings at once: [key => value]. Unknown keys are ignored.
     */
    public function set(array $values): void
    {
        $definitions = $this->definitions();

        foreach ($values as $key => $value) {
            if (! isset($definitions[$key])) {
                continue;
            }
            $type = $definitions[$key]['type'];
            $stored = match ($type) {
                'boolean' => $value ? '1' : '0',
                'lines' => is_array($value) ? implode("\n", $value) : (string) $value,
                default => $value === null ? null : (string) $value,
            };
            Setting::updateOrCreate(['key' => $key], ['value' => $stored]);
        }

        $this->flush();
    }

    /** Forget cached values (after saving, or in tests). */
    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
        $this->saved = null;
    }

    /** Options of a select field, e.g. the themes for default_theme. */
    public function options(string $key): array
    {
        $field = $this->definitions()[$key] ?? [];
        if (($field['options_from'] ?? null) === 'themes') {
            return Arr::map(config('themes.list'), fn ($theme) => $theme['label']);
        }

        return $field['options'] ?? [];
    }

    /** @return array<string, string|null> */
    private function saved(): array
    {
        if ($this->saved !== null) {
            return $this->saved;
        }

        try {
            return $this->saved = Cache::rememberForever(
                self::CACHE_KEY,
                fn () => Setting::query()->pluck('value', 'key')->all()
            );
        } catch (QueryException) {
            // The settings table does not exist yet (fresh install before
            // "php artisan migrate"): fall back to the defaults.
            return $this->saved = [];
        }
    }

    private function cast(string $type, mixed $raw): mixed
    {
        return match ($type) {
            'boolean' => filter_var($raw, FILTER_VALIDATE_BOOLEAN),
            'lines' => array_values(array_filter(
                array_map('trim', preg_split('/\R/', (string) $raw) ?: []),
                fn ($line) => $line !== ''
            )),
            default => $raw === null ? '' : (string) $raw,
        };
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Support\Settings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Dashboard → Settings. One tab per group in config/settings.php
 * (General, Contact & social, Home page texts, SEO).
 */
class SettingController extends Controller implements HasMiddleware
{
    public function __construct(private Settings $settings) {}

    public static function middleware(): array
    {
        return [
            new Middleware('can:settings.view', only: ['edit']),
            new Middleware('can:settings.update', only: ['update']),
        ];
    }

    public function edit(?string $group = null): View
    {
        $groups = config('settings.groups');
        $group ??= array_key_first($groups);
        abort_unless(isset($groups[$group]), 404);

        $values = [];
        foreach ($groups[$group]['fields'] as $key => $field) {
            $value = $this->settings->get($key);
            $values[$key] = $field['type'] === 'lines' ? implode("\n", $value) : $value;
        }

        return view('admin.settings.edit', [
            'groups' => $groups,
            'group' => $group,
            'fields' => $groups[$group]['fields'],
            'values' => $values,
            'settings' => $this->settings,
        ]);
    }

    public function update(Request $request, string $group): RedirectResponse
    {
        $groups = config('settings.groups');
        abort_unless(isset($groups[$group]), 404);
        $fields = $groups[$group]['fields'];

        $rules = [];
        $attributes = [];
        foreach ($fields as $key => $field) {
            $rules[$key] = $field['rules'] ?? ['nullable'];
            $attributes[$key] = Str::lower($field['label']);
            if ($field['type'] === 'select') {
                $rules[$key][] = Rule::in(array_map('strval', array_keys($this->settings->options($key))));
            }
            if ($field['type'] === 'boolean') {
                $rules[$key] = ['nullable', 'boolean'];
            }
            if ($field['type'] === 'image') {
                $rules["remove_{$key}"] = ['nullable', 'boolean'];
            }
        }

        $validated = $request->validate($rules, [], $attributes);

        $values = [];
        foreach ($fields as $key => $field) {
            $values[$key] = match ($field['type']) {
                'boolean' => $request->boolean($key),
                'image' => $this->storeImage($request, $key),
                default => $validated[$key] ?? '',
            };
        }

        $this->settings->set($values);
        ActivityLog::record('updated', description: 'Updated settings: '.$groups[$group]['label']);

        return redirect()->route('admin.settings.edit', $group)->with('success', $groups[$group]['label'].' settings saved.');
    }

    /** Upload / remove an image setting (e.g. the social sharing image). */
    private function storeImage(Request $request, string $key): string
    {
        $current = (string) $this->settings->get($key);

        if ($request->hasFile($key)) {
            if ($current !== '') {
                Storage::disk('public')->delete($current);
            }

            return $request->file($key)->store('settings', 'public');
        }

        if ($request->boolean("remove_{$key}") && $current !== '') {
            Storage::disk('public')->delete($current);

            return '';
        }

        return $current;
    }
}

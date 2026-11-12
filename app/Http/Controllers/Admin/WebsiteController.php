<?php

namespace App\Http\Controllers\Admin;

use App\Models\Website;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

/**
 * Dashboard → Websites (the "Websites we launched" section).
 */
class WebsiteController extends ResourceController
{
    protected static string $area = 'websites';

    protected function model(): string
    {
        return Website::class;
    }

    protected function labels(): array
    {
        return [
            'singular' => 'Website',
            'plural' => 'Websites',
            'route' => 'websites',
            'description' => 'Client websites shown in the "Websites we launched" section of the home page.',
        ];
    }

    protected function searchable(): array
    {
        return ['name', 'url', 'client', 'category'];
    }

    protected function columns(): array
    {
        return [
            'Name' => 'name',
            'Address' => 'display_url',
            'Category' => 'category',
            'Status' => 'status_label',
        ];
    }

    protected function fields(?Model $record): array
    {
        return [
            'name' => [
                'section' => 'Website', 'label' => 'Name', 'type' => 'text', 'width' => 'half',
                'rules' => ['required', 'string', 'max:120'],
            ],
            'url' => [
                'section' => 'Website', 'label' => 'Address (URL)', 'type' => 'url', 'width' => 'half',
                'rules' => ['required', 'url', 'max:255'],
                'help' => 'The full address, starting with https://',
            ],
            'client' => [
                'section' => 'Website', 'label' => 'Client', 'type' => 'text', 'width' => 'half',
                'rules' => ['nullable', 'string', 'max:120'],
            ],
            'category' => [
                'section' => 'Website', 'label' => 'Category', 'type' => 'text', 'width' => 'half',
                'rules' => ['nullable', 'string', 'max:60'],
                'help' => 'E.g. "E-commerce", "Clinic", "Corporate".',
            ],
            'status' => [
                'section' => 'Website', 'label' => 'Status', 'type' => 'select', 'width' => 'half',
                'options' => Website::STATUSES,
                'rules' => ['required', Rule::in(array_keys(Website::STATUSES))],
            ],
            'launched_on' => [
                'section' => 'Website', 'label' => 'Launch date', 'type' => 'date', 'width' => 'half',
                'rules' => ['nullable', 'date'],
            ],
            'description' => [
                'section' => 'Website', 'label' => 'Short description', 'type' => 'textarea',
                'rules' => ['nullable', 'string', 'max:400'],
            ],
            'screenshot_path' => [
                'section' => 'Look', 'label' => 'Screenshot', 'type' => 'image',
                'directory' => 'websites',
                'rules' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
                'help' => 'A screenshot of the home page (16:10 looks best; JPG, PNG or WebP, max 4 MB). Without one, the first letter of the name is shown.',
            ],
            'is_published' => [
                'section' => 'Visibility', 'label' => 'Published (visible to visitors)', 'type' => 'boolean',
                'publish' => true, 'default' => true,
            ],
        ];
    }

    protected function publicUrl(Model $record): ?string
    {
        return $record->url;
    }
}

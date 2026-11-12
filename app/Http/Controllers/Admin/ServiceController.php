<?php

namespace App\Http\Controllers\Admin;

use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

/**
 * Dashboard → Services (the "What we build" cards).
 */
class ServiceController extends ResourceController
{
    protected static string $area = 'services';

    protected function model(): string
    {
        return Service::class;
    }

    protected function labels(): array
    {
        return [
            'singular' => 'Service',
            'plural' => 'Services',
            'route' => 'services',
            'description' => 'The cards in the "What we build" section. Drag to change their order.',
        ];
    }

    protected function searchable(): array
    {
        return ['title', 'description'];
    }

    protected function columns(): array
    {
        return [
            'Title' => 'title',
            'Artwork' => fn (Service $service) => Service::VISUALS[$service->visual] ?? $service->visual,
            'Tags' => fn (Service $service) => implode(', ', $service->tags ?? []),
            'Size' => fn (Service $service) => $service->is_wide ? 'Wide' : 'Normal',
        ];
    }

    protected function fields(?Model $record): array
    {
        return [
            'title' => [
                'section' => 'Service', 'label' => 'Title', 'type' => 'text', 'width' => 'half',
                'rules' => ['required', 'string', 'max:80'],
            ],
            'visual' => [
                'section' => 'Service', 'label' => 'Icon & animation', 'type' => 'select', 'width' => 'half',
                'options' => Service::VISUALS,
                'rules' => ['required', Rule::in(array_keys(Service::VISUALS))],
            ],
            'description' => [
                'section' => 'Service', 'label' => 'Description', 'type' => 'textarea',
                'rules' => ['required', 'string', 'max:300'],
            ],
            'tags' => [
                'section' => 'Service', 'label' => 'Tags', 'type' => 'tags',
                'rules' => ['nullable', 'string', 'max:200'],
                'help' => 'Separate with commas, e.g. "REST, Auth, PWA".',
            ],
            'is_wide' => [
                'section' => 'Visibility', 'label' => 'Wide card (two columns on large screens)', 'type' => 'boolean',
            ],
            'is_published' => [
                'section' => 'Visibility', 'label' => 'Published (visible to visitors)', 'type' => 'boolean',
                'publish' => true, 'default' => true,
            ],
        ];
    }
}

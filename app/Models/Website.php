<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use App\Models\Concerns\RecordsActivity;
use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A website launched for a client (the "Websites" section).
 */
#[Fillable([
    'name', 'url', 'client', 'category', 'description', 'screenshot_path',
    'status', 'launched_on', 'is_published', 'sort_order',
])]
class Website extends Model
{
    use HasFactory, Publishable, RecordsActivity, Sortable;

    public const STATUSES = [
        'live' => 'Live',
        'in_progress' => 'In development',
        'archived' => 'Archived',
    ];

    protected function casts(): array
    {
        return [
            'launched_on' => 'date',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected function screenshotUrl(): Attribute
    {
        return Attribute::get(fn () => $this->screenshot_path ? asset('storage/'.$this->screenshot_path) : null);
    }

    /** "https://www.example.com/shop" → "example.com/shop" (for display). */
    protected function displayUrl(): Attribute
    {
        return Attribute::get(function () {
            $host = parse_url($this->url, PHP_URL_HOST) ?: $this->url;
            $path = rtrim((string) parse_url($this->url, PHP_URL_PATH), '/');

            return preg_replace('/^www\./', '', $host).$path;
        });
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn () => self::STATUSES[$this->status] ?? ucfirst((string) $this->status));
    }
}

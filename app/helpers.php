<?php

/*
|--------------------------------------------------------------------------
| Global helper functions (loaded by composer.json → autoload.files)
|--------------------------------------------------------------------------
*/

use App\Support\Settings;
use Illuminate\Support\Str;

if (! function_exists('setting')) {
    /**
     * A site setting from Dashboard → Settings, e.g. setting('contact_email').
     * Returns the default from config/settings.php until it is changed.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return app(Settings::class)->get($key, $default);
    }
}

if (! function_exists('safe_markdown')) {
    /**
     * Markdown → HTML for text typed in the dashboard. Raw HTML is removed and
     * unsafe links (javascript:…) are blocked, so it is safe to echo with {!! !!}.
     */
    function safe_markdown(?string $text): string
    {
        return Str::markdown((string) $text, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Dashboard → Help: shows docs/USER_GUIDE.md (the guide for site editors).
 */
class HelpController extends Controller
{
    public function __invoke(): View
    {
        $path = base_path('docs/USER_GUIDE.md');

        $html = File::exists($path)
            ? Str::markdown(File::get($path), ['html_input' => 'strip', 'allow_unsafe_links' => false])
            : '<p>The user guide (docs/USER_GUIDE.md) is missing.</p>';

        // Plain text of a heading: tags removed and entities (&amp;) turned back into characters.
        $plain = fn (string $html) => html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5);

        // Give every heading an id so the table of contents can link to it.
        $html = preg_replace_callback('/<h([23])>(.*?)<\/h\1>/', function (array $m) use ($plain) {
            $id = 'help-'.Str::slug($plain($m[2]));

            return "<h{$m[1]} id=\"{$id}\">{$m[2]}</h{$m[1]}>";
        }, $html);

        preg_match_all('/<h2 id="([^"]+)">(.*?)<\/h2>/', $html, $headings, PREG_SET_ORDER);

        return view('admin.help', [
            'html' => $html,
            'toc' => array_map(fn ($h) => ['id' => $h[1], 'title' => $plain($h[2])], $headings),
        ]);
    }
}

<?php

/*
|--------------------------------------------------------------------------
| Color themes
|--------------------------------------------------------------------------
| The single list of themes. It drives:
|   - the theme menu on the website and in the dashboard (partials/theme-switch)
|   - the "Default theme" choice in Dashboard → Settings
|
| Each theme's colors live in public/css/base.css under :root[data-theme="…"].
| "system" is not a real palette: it follows the visitor's device
| (dark → "dark", light → "light").
|
| To add a theme: add an entry here AND a :root[data-theme="key"] block in base.css.
*/

return [

    // Used when nothing is saved in Settings yet.
    'default' => 'system',

    'list' => [
        'system' => [
            'label' => 'System',
            'description' => 'Follow your device',
            'swatch' => ['#08090d', '#f6f7fb'],
        ],
        'dark' => [
            'label' => 'Dark',
            'description' => 'The original look',
            'swatch' => ['#08090d', '#6ee7ff'],
        ],
        'light' => [
            'label' => 'Light',
            'description' => 'Bright and clean',
            'swatch' => ['#f6f7fb', '#0e7490'],
        ],
        'dim' => [
            'label' => 'Dim',
            'description' => 'Softer dark, easy on the eyes',
            'swatch' => ['#1b1e25', '#7cc4d6'],
        ],
        'sepia' => [
            'label' => 'Sepia',
            'description' => 'Warm paper, less blue light',
            'swatch' => ['#f4ecd8', '#8a4f0f'],
        ],
        'rakiza' => [
            'label' => 'Rakiza',
            'description' => 'Brand green and gold',
            'swatch' => ['#06120d', '#d9b25a'],
        ],
    ],

];

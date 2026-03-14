<?php

declare(strict_types=1);

return [
    'generator' => [
        'namespace' => 'App\\Mason',
        'views_path' => 'mason',
    ],
    'preview' => [
        'layout' => 'layouts.mason-preview',
    ],
    'entry' => [
        'layout' => 'mason::iframe-entry', // Set to your layout view path, e.g., 'layouts.entry'
    ],
    'routes' => [
        'middleware' => ['web', 'auth'],
    ],
];

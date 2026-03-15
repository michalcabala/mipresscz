<?php

return [
    'feeds' => [
        'main' => [
            'items' => [\MiPressCz\Core\Models\Entry::class, 'getFeedItems'],
            'url' => '/feed.xml',
            'title' => config('app.name'),
            'description' => config('app.name'),
            'language' => 'cs-CZ',
            'image' => '',
            'format' => 'rss',
            'view' => 'feed::rss',
            'type' => 'application/rss+xml',
            'contentType' => 'application/rss+xml',
        ],
        'locale' => [
            'items' => [\MiPressCz\Core\Models\Entry::class, 'getFeedItems'],
            'url' => '/{locale}/feed.xml',
            'title' => config('app.name'),
            'description' => config('app.name'),
            'language' => 'cs-CZ',
            'image' => '',
            'format' => 'rss',
            'view' => 'feed::rss',
            'type' => 'application/rss+xml',
            'contentType' => 'application/rss+xml',
        ],
    ],
];

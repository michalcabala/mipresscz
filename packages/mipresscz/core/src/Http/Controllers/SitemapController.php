<?php

namespace MiPressCz\Core\Http\Controllers;

use Illuminate\Http\Response;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;

class SitemapController
{
    public function index(): Response
    {
        $collections = Collection::query()
            ->whereHas('entries', fn ($q) => $q->published())
            ->get();

        $defaultLocale = locales()->getDefaultCode();

        $entries = Entry::query()
            ->with('collection')
            ->published()
            ->whereHas('collection')
            ->orderBy('updated_at', 'desc')
            ->get();

        $content = view('mipresscz-core::sitemap.index', compact('entries', 'defaultLocale'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }
}

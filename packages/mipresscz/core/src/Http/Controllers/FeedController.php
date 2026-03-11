<?php

namespace MiPressCz\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use MiPressCz\Core\Models\Entry;

class FeedController
{
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();
        $defaultLocale = locales()->getDefaultCode();

        $entries = Entry::query()
            ->with('collection')
            ->published()
            ->whereHas('collection')
            ->where('locale', $locale)
            ->orderBy('published_at', 'desc')
            ->limit(50)
            ->get();

        $content = view('mipresscz-core::feed.rss', compact('entries', 'locale', 'defaultLocale'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/rss+xml; charset=utf-8',
        ]);
    }
}

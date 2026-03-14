<?php

namespace MiPressCz\Core\View\Composers;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Services\CacheService;

class NavComposer
{
    public function __construct(
        private CacheService $cache,
    ) {}

    public function compose(View $view): void
    {
        $locale = app()->getLocale();
        $viewName = $view->getName();

        if (str_contains($viewName, 'header')) {
            $view->with('navEntries', $this->getHeaderNav($locale));
        }

        if (str_contains($viewName, 'footer')) {
            $view->with('footerEntries', $this->getFooterNav($locale));
        }
    }

    private function getHeaderNav(string $locale): Collection
    {
        return $this->cache->getNav('header', $locale, function () use ($locale) {
            return Entry::query()
                ->whereHas('collection', fn ($q) => $q->where('handle', 'pages'))
                ->where('is_homepage', false)
                ->published()
                ->where('locale', $locale)
                ->orderBy('title')
                ->limit(8)
                ->get(['id', 'title', 'uri']);
        });
    }

    private function getFooterNav(string $locale): Collection
    {
        return $this->cache->getNav('footer', $locale, function () use ($locale) {
            return Entry::query()
                ->whereHas('collection', fn ($q) => $q->where('handle', 'pages'))
                ->where('is_homepage', false)
                ->published()
                ->where('locale', $locale)
                ->orderBy('title')
                ->limit(5)
                ->get(['id', 'title', 'uri']);
        });
    }
}

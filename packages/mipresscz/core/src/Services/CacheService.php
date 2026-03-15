<?php

namespace MiPressCz\Core\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    public const PREFIX = 'mipress.page.';

    public const NAV_PREFIX = 'mipress.nav.';

    public const INDEX_KEY = 'mipress.page_index';

    public const TTL = 3600;

    public function getPageKey(string $uri, string $locale): string
    {
        return self::PREFIX.sha1($locale.':'.$uri);
    }

    public function getNavKey(string $type, string $locale): string
    {
        return self::NAV_PREFIX.$type.'.'.$locale;
    }

    public function getPage(string $uri, string $locale): ?string
    {
        return Cache::get($this->getPageKey($uri, $locale));
    }

    public function putPage(string $uri, string $locale, string $html): void
    {
        $key = $this->getPageKey($uri, $locale);
        Cache::put($key, $html, self::TTL);
        $this->trackKey($key);
    }

    public function getNav(string $type, string $locale, \Closure $callback): mixed
    {
        return Cache::remember($this->getNavKey($type, $locale), self::TTL, $callback);
    }

    public function flushPage(string $uri, string $locale): void
    {
        $key = $this->getPageKey($uri, $locale);
        Cache::forget($key);
        $this->untrackKey($key);
    }

    public function flushNav(): void
    {
        $locales = locales()->getActiveCodes();

        foreach ($locales as $locale) {
            Cache::forget($this->getNavKey('header', $locale));
            Cache::forget($this->getNavKey('footer', $locale));
            Cache::forget($this->getNavKey('menu.primary', $locale));
            Cache::forget($this->getNavKey('menu.footer', $locale));
        }
    }

    public function flushAll(): void
    {
        $this->flushNav();
        $this->flushAllPages();
    }

    public function flushAllPages(): void
    {
        $keys = Cache::get(self::INDEX_KEY, []);

        foreach ($keys as $key => $v) {
            Cache::forget($key);
        }

        Cache::forget(self::INDEX_KEY);
    }

    public function flushEntry(string $uri, ?string $locale = null): void
    {
        if ($locale) {
            $this->flushPage($uri, $locale);

            return;
        }

        // Flush for all active locales
        foreach (locales()->getActiveCodes() as $code) {
            $this->flushPage($uri, $code);
        }
    }

    private function trackKey(string $key): void
    {
        $keys = Cache::get(self::INDEX_KEY, []);
        $keys[$key] = true;
        Cache::forever(self::INDEX_KEY, $keys);
    }

    private function untrackKey(string $key): void
    {
        $keys = Cache::get(self::INDEX_KEY, []);
        unset($keys[$key]);
        Cache::forever(self::INDEX_KEY, $keys);
    }
}

<?php

namespace MiPressCz\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use MiPressCz\Core\Services\CacheService;
use Symfony\Component\HttpFoundation\Response;

class PageCache
{
    public function __construct(
        private CacheService $cache,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->shouldCache($request)) {
            return $next($request);
        }

        $uri = '/'.ltrim((string) $request->route('uri', ''), '/');
        $locale = app()->getLocale();

        $cached = $this->cache->getPage($uri, $locale);

        if ($cached !== null) {
            return response($cached, 200, [
                'Content-Type' => 'text/html; charset=UTF-8',
                'X-Page-Cache' => 'HIT',
            ]);
        }

        $response = $next($request);

        if ($this->shouldStore($response)) {
            $this->cache->putPage($uri, $locale, $response->getContent());
            $response->headers->set('X-Page-Cache', 'MISS');
        }

        return $response;
    }

    private function shouldCache(Request $request): bool
    {
        // Only cache GET requests
        if (! $request->isMethod('GET')) {
            return false;
        }

        // Skip if authenticated (admin users see different content)
        if ($request->user()) {
            return false;
        }

        // Skip if there's a query string (search, pagination, etc.)
        if ($request->getQueryString()) {
            return false;
        }

        return true;
    }

    private function shouldStore(Response $response): bool
    {
        return $response->getStatusCode() === 200
            && $response->headers->get('Content-Type', '') !== ''
            && str_contains($response->headers->get('Content-Type', ''), 'text/html');
    }
}

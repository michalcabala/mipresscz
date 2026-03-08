<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetFrontendLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $localeParam = $request->route('locale') ?? '';

        // Single-locale mode: redirect prefixed URLs to unprefixed
        if ($localeParam !== '' && ! locales()->shouldPrefixUrls()) {
            $uri = $request->route('uri') ?? '';

            return redirect('/'.$uri, 301);
        }

        if ($localeParam !== '' && locales()->findByCode($localeParam)?->is_frontend_available) {
            app()->setLocale($localeParam);
        } else {
            app()->setLocale(locales()->getDefaultCode());
        }

        return $next($request);
    }
}

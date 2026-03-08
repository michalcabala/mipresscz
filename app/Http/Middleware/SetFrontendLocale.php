<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetFrontendLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale') ?? '';

        if ($locale !== '' && locales()->findByCode($locale)?->is_frontend_available) {
            app()->setLocale($locale);
        } else {
            app()->setLocale(locales()->getDefaultCode());
        }

        return $next($request);
    }
}

<?php

use App\Services\LocaleService;

if (! function_exists('locales')) {
    function locales(): LocaleService
    {
        return app(LocaleService::class);
    }
}

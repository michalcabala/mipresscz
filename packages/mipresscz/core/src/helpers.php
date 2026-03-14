<?php

use MiPressCz\Core\Services\LocaleService;
use MiPressCz\Core\Services\TemplateManager;

if (! function_exists('locales')) {
    function locales(): LocaleService
    {
        return app(LocaleService::class);
    }
}

if (! function_exists('active_template')) {
    function active_template(): string
    {
        return app(TemplateManager::class)->getActive();
    }
}

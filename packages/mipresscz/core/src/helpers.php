<?php

use MiPressCz\Core\Services\LocaleService;
use MiPressCz\Core\Services\NavMenuService;
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

if (! function_exists('menu')) {
    /**
     * Get the nested menu tree for a given location handle.
     *
     * @return array<int, array<string, mixed>>
     */
    function menu(string $locationHandle): array
    {
        return app(NavMenuService::class)->getMenuTree($locationHandle);
    }
}

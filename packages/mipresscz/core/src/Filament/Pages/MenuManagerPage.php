<?php

namespace MiPressCz\Core\Filament\Pages;

use Filament\Panel;
use NoteBrainsLab\FilamentMenuManager\Pages\MenuManagerPage as BaseMenuManagerPage;

class MenuManagerPage extends BaseMenuManagerPage
{
    public static function canAccess(): bool
    {
        return auth()->user()?->can('manage.menus') ?? false;
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'menu-manager';
    }
}

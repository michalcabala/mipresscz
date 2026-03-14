<?php

namespace MiPressCz\Core\Filament\Plugins;

use Filament\Panel;
use MiPressCz\Core\Filament\Pages\MenuManagerPage;
use NoteBrainsLab\FilamentMenuManager\FilamentMenuManagerPlugin;

class MenuManagerPlugin extends FilamentMenuManagerPlugin
{
    public function register(Panel $panel): void
    {
        $panel->pages([MenuManagerPage::class]);
    }
}

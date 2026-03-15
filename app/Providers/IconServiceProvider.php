<?php

namespace App\Providers;

use Filament\Support\Facades\FilamentIcon;
use Filament\View\PanelsIconAlias;
use Illuminate\Support\ServiceProvider;

class IconServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        FilamentIcon::register([
            PanelsIconAlias::PAGES_DASHBOARD_NAVIGATION_ITEM => 'far-gauge-high',
        ]);
    }
}

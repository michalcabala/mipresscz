<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use MuhammadNawlo\FilamentSitemapGenerator\Pages\SitemapSettingsPage as BaseSitemapSettingsPage;

class SitemapSettingsPage extends BaseSitemapSettingsPage
{
    public static function getNavigationIcon(): string|\BackedEnum|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'far-gear';
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use MuhammadNawlo\FilamentSitemapGenerator\Pages\SitemapGeneratorPage as BaseSitemapGeneratorPage;

class SitemapGeneratorPage extends BaseSitemapGeneratorPage
{
    public static function getNavigationIcon(): string|\BackedEnum|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'far-sitemap';
    }
}

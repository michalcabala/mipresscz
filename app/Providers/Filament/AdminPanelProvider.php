<?php

namespace App\Providers\Filament;

use App\Filament\Pages\SitemapGeneratorPage;
use App\Filament\Pages\SitemapSettingsPage;
use Awcodes\Botly\BotlyPlugin;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Illuminate\Support\Facades\Schema;
use MiPressCz\Core\Filament\Resources\Entries\EntryResource;
use MiPressCz\Core\Filament\Resources\Terms\TermResource;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\Taxonomy;
use MiPressCz\Core\Providers\MiPressCzAdminPanelProvider;

class AdminPanelProvider extends MiPressCzAdminPanelProvider
{
    protected function configurePlugins(Panel $panel): Panel
    {
        return parent::configurePlugins($panel)
            ->plugin(
                BotlyPlugin::make()
                    ->navigationGroup(__('settings.navigation_group'))
                    ->navigationLabel(__('botly::botly.navigation.label'))
                    ->navigationIcon('far-robot'),
            )
            ->pages([
                SitemapGeneratorPage::class,
                SitemapSettingsPage::class,
            ]);
    }

    /**
     * @return array<int, mixed>
     */
    protected function getCollectionResources(): array
    {
        if (! Schema::hasTable('collections')) {
            return [];
        }

        return Collection::query()
            ->where('is_active', true)
            ->orderBy('title')
            ->get()
            ->map(fn (Collection $collection, int $index) => EntryResource::make($collection->handle)
                ->slug($collection->handle)
                ->collectionHandle($collection->handle)
                ->navigationLabel($collection->title)
                ->navigationIcon($collection->icon ?? 'far-file-lines')
                ->navigationSort($index + 1)
            )
            ->all();
    }

    /**
     * @return array<int, class-string>
     */
    protected function getMenuModelSources(): array
    {
        return [
            Entry::class,
        ];
    }

    /**
     * @return array<int, mixed>
     */
    protected function getTaxonomyResources(): array
    {
        if (! Schema::hasTable('collection_taxonomy')) {
            return [];
        }

        return Taxonomy::query()
            ->where('is_active', true)
            ->whereHas('collections', fn ($query) => $query->where('is_active', true))
            ->with(['collections' => fn ($query) => $query->where('is_active', true)->orderBy('title')])
            ->orderBy('title')
            ->get()
            ->map(fn (Taxonomy $taxonomy, int $index) => TermResource::make($taxonomy->handle)
                ->slug("terms/{$taxonomy->handle}")
                ->taxonomyHandle($taxonomy->handle)
                ->navigationLabel($taxonomy->title)
                ->navigationParentItem($taxonomy->collections->first()?->title)
                ->navigationIcon('far-tags')
                ->navigationSort($index + 1)
            )
            ->all();
    }

    /**
     * @return array<int, NavigationItem>
     */
    protected function getTaxonomyNavigationItems(): array
    {
        if (! Schema::hasTable('collection_taxonomy')) {
            return [];
        }

        return Taxonomy::query()
            ->where('is_active', true)
            ->whereHas('collections', fn ($query) => $query->where('is_active', true))
            ->with(['collections' => fn ($query) => $query->where('is_active', true)->orderBy('title')])
            ->orderBy('title')
            ->get()
            ->map(fn (Taxonomy $taxonomy, int $index) => NavigationItem::make($taxonomy->title)
                ->group(__('content.entries.navigation_group'))
                ->parentItem($taxonomy->collections->first()?->title)
                ->icon('far-tags')
                ->sort($index + 1)
                ->url(fn (): string => TermResource::getUrl('index', configuration: $taxonomy->handle))
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.terms.*')
                    && Filament::getCurrentResourceConfigurationKey() === $taxonomy->handle))
            ->all();
    }
}

<?php

namespace App\Providers\Filament;

use App\Models\Collection;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\Facades\Schema;
use MiPressCz\Core\Filament\Resources\Entries\EntryResource;
use MiPressCz\Core\Filament\Resources\Terms\TermResource;
use MiPressCz\Core\Models\Taxonomy;
use MiPressCz\Core\Providers\MiPressCzAdminPanelProvider;

class AdminPanelProvider extends MiPressCzAdminPanelProvider
{
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
                ->navigationIcon($collection->icon ?? 'fal-file-lines')
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

        $contentGroup = __('content.entries.navigation_group');

        return Collection::query()
            ->where('is_active', true)
            ->with(['taxonomies' => fn ($q) => $q->where('is_active', true)->orderBy('title')])
            ->get()
            ->flatMap(fn (Collection $collection) => $collection->taxonomies->map(
                fn (Taxonomy $taxonomy) => NavigationItem::make("taxonomy-{$collection->handle}-{$taxonomy->handle}")
                    ->label($taxonomy->title)
                    ->icon('fal-tags')
                    ->group($contentGroup)
                    ->parentItem($collection->title)
                    ->url(fn (): string => TermResource::getUrl('index').'?'.http_build_query(['taxonomy_id' => $taxonomy->id]))
                    ->isActiveWhen(fn (): bool => request()->query('taxonomy_id') === $taxonomy->id)
                    ->sort(90)
            ))
            ->all();
    }
}

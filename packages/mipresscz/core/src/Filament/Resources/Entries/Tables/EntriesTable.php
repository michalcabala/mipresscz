<?php

namespace MiPressCz\Core\Filament\Resources\Entries\Tables;

use Awcodes\Curator\Components\Tables\CuratorColumn;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Schema;
use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Filament\Resources\Entries\EntryResource;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\Taxonomy;
use MiPressCz\Core\Models\Term;

class EntriesTable
{
    public static function configure(Table $table): Table
    {
        $collectionHandle = EntryResource::getCollectionHandle();
        $scopedCollection = static::getScopedCollection($collectionHandle);
        $hasCollection = $scopedCollection !== null;
        $collectionTaxonomies = static::getScopedCollectionTaxonomies($scopedCollection);
        $taxonomyColumns = static::getTaxonomyColumns($collectionTaxonomies);
        $taxonomyFilters = static::getTaxonomyFilters($collectionTaxonomies);

        return $table
            ->columns([
                CuratorColumn::make('featuredImage')
                    ->label(__('content.entry_fields.image'))
                    ->imageSize(32)
                    ->width('1%'),
                TextColumn::make('title')
                    ->label(__('content.entry_fields.title'))
                    ->icon(fn (Entry $record): ?string => $record->is_homepage ? 'fal-house' : null)
                    ->iconColor('success')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->slug),
                TextColumn::make('collection.title')
                    ->label(__('content.entry_fields.collection'))
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->visible(! $hasCollection),
                TextColumn::make('status')
                    ->label(__('content.entry_fields.status'))
                    ->badge()
                    ->icon(fn (EntryStatus $state): string => $state->icon())
                    ->color(fn (EntryStatus $state): string => $state->color()),
                ImageColumn::make('locale')
                    ->label(__('content.entry_fields.locale'))
                    ->state(function (Entry $record): array {
                        return collect([$record->locale])
                            ->merge($record->translations->pluck('locale'))
                            ->unique()
                            ->sort()
                            ->map(function (string $locale): ?string {
                                $flag = locales()->findByCode($locale)?->flag;

                                return $flag ? asset('assets/flags/'.$flag) : null;
                            })
                            ->filter()
                            ->values()
                            ->all();
                    })
                    ->imageHeight(20)
                    ->circular()
                    ->stacked()
                    ->visible(fn (): bool => locales()->isMultilingual()),
                TextColumn::make('author.name')
                    ->label(__('content.entry_fields.author'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('published_at')
                    ->label(__('content.entry_fields.published_at'))
                    ->isoDateTime('LLL')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label(__('content.entry_fields.updated_at'))
                    ->isoDateTime('LLL')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ...$taxonomyColumns,
            ])
            ->defaultSort(static::getDefaultSortField($scopedCollection), static::getDefaultSortDirection($scopedCollection))
            ->reorderable('order', static::isReorderable($scopedCollection), 'asc')
            ->filters([
                SelectFilter::make('collection_id')
                    ->label(__('content.entry_fields.collection'))
                    ->relationship('collection', 'title')
                    ->visible(! $hasCollection),
                SelectFilter::make('status')
                    ->label(__('content.entry_fields.status'))
                    ->options(EntryStatus::class),
                SelectFilter::make('locale')
                    ->label(__('content.entry_fields.locale'))
                    ->visible(fn (): bool => locales()->isMultilingual())
                    ->options(fn (): array => locales()->toSelectOptions())
                    ->query(fn (Builder $query, array $data): Builder => blank($data['value'])
                        ? $query
                        : $query->where(fn (Builder $q) => $q
                            ->where('locale', $data['value'])
                            ->orWhereHas('translations', fn (Builder $tq) => $tq->where('locale', $data['value'])))
                    ),
                ...$taxonomyFilters,
            ])
            ->recordActions([
                EditAction::make(),

                DeleteAction::make()
                    ->before(function (Entry $record, DeleteAction $action): void {
                        if ($record->is_homepage) {
                            Notification::make()
                                ->title(__('content.messages.cannot_delete_homepage'))
                                ->danger()
                                ->send();
                            $action->cancel();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @return array<int, TextColumn>
     */
    protected static function getTaxonomyColumns(SupportCollection $taxonomies): array
    {
        if ($taxonomies->isEmpty()) {
            return [];
        }

        return $taxonomies
            ->map(fn (Taxonomy $taxonomy): TextColumn => TextColumn::make("taxonomy_{$taxonomy->handle}")
                ->label($taxonomy->title)
                ->state(fn (Entry $record): ?string => static::getTaxonomyColumnState($record, $taxonomy))
                ->toggleable()
            )
            ->all();
    }

    /**
     * @return SupportCollection<int, Taxonomy>
     */
    protected static function getScopedCollection(?string $collectionHandle): ?Collection
    {
        if (blank($collectionHandle)) {
            return null;
        }

        if ((! Schema::hasTable('collections')) || (! Schema::hasTable('collection_taxonomy'))) {
            return null;
        }

        if ((! Schema::hasTable('taxonomies')) || (! Schema::hasTable('terms'))) {
            return null;
        }

        return Collection::query()
            ->where('handle', $collectionHandle)
            ->with([
                'taxonomies' => fn ($query) => $query
                    ->where('is_active', true)
                    ->orderBy('title'),
            ])
            ->first();
    }

    /**
     * @return SupportCollection<int, Taxonomy>
     */
    protected static function getScopedCollectionTaxonomies(?Collection $collection): SupportCollection
    {
        return $collection?->taxonomies ?? collect();
    }

    protected static function getDefaultSortField(?Collection $collection): string
    {
        return $collection?->sort_field ?: 'published_at';
    }

    protected static function getDefaultSortDirection(?Collection $collection): string
    {
        if (! $collection) {
            return 'desc';
        }

        return $collection->sort_direction ?: 'asc';
    }

    protected static function isReorderable(?Collection $collection): bool
    {
        return $collection !== null;
    }

    protected static function getTaxonomyColumnState(Entry $record, Taxonomy $taxonomy): ?string
    {
        $titles = $record->terms
            ->filter(fn ($term) => $term->taxonomy_id === $taxonomy->getKey())
            ->pluck('title')
            ->filter()
            ->unique()
            ->implode(', ');

        return blank($titles) ? null : $titles;
    }

    /**
     * @return array<int, BaseFilter>
     */
    protected static function getTaxonomyFilters(SupportCollection $taxonomies): array
    {
        if ($taxonomies->isEmpty()) {
            return [];
        }

        return $taxonomies
            ->map(fn (Taxonomy $taxonomy): BaseFilter => $taxonomy->is_hierarchical
                ? static::makeHierarchicalTaxonomyFilter($taxonomy)
                : static::makeSelectTaxonomyFilter($taxonomy)
            )
            ->all();
    }

    protected static function makeSelectTaxonomyFilter(Taxonomy $taxonomy): SelectFilter
    {
        return SelectFilter::make("taxonomy_{$taxonomy->handle}")
            ->label($taxonomy->title)
            ->options(fn (): array => static::getTaxonomyTermOptions($taxonomy))
            ->query(fn (Builder $query, array $data): Builder => blank($data['value'])
                ? $query
                : static::applyTaxonomyTermFilter($query, $taxonomy, $data['value'])
            );
    }

    protected static function makeHierarchicalTaxonomyFilter(Taxonomy $taxonomy): Filter
    {
        return Filter::make("taxonomy_{$taxonomy->handle}")
            ->label($taxonomy->title)
            ->schema([
                SelectTree::make('value')
                    ->label($taxonomy->title)
                    ->query(fn () => static::getTaxonomyTermsQuery($taxonomy), 'title', 'parent_id')
                    ->multiple(false)
                    ->searchable()
                    ->enableBranchNode()
                    ->parentNullValue(null),
            ])
            ->indicateUsing(fn (array $data): array => static::getHierarchicalTaxonomyFilterIndicators($taxonomy, $data))
            ->query(fn (Builder $query, array $data): Builder => blank($data['value'])
                ? $query
                : static::applyTaxonomyTermFilter($query, $taxonomy, $data['value'])
            );
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, string>
     */
    protected static function getHierarchicalTaxonomyFilterIndicators(Taxonomy $taxonomy, array $data): array
    {
        $value = $data['value'] ?? null;

        if (blank($value)) {
            return [];
        }

        $label = static::getTaxonomyTermsQuery($taxonomy)
            ->whereKey($value)
            ->value('title');

        if (blank($label)) {
            return [];
        }

        return [
            'value' => "{$taxonomy->title}: {$label}",
        ];
    }

    /**
     * @return array<string, string>
     */
    protected static function getTaxonomyTermOptions(Taxonomy $taxonomy): array
    {
        return static::getTaxonomyTermsQuery($taxonomy)
            ->where('is_active', true)
            ->orderBy('title')
            ->pluck('title', 'id')
            ->all();
    }

    protected static function getTaxonomyTermsQuery(Taxonomy $taxonomy): Builder
    {
        return Term::query()
            ->where('taxonomy_id', $taxonomy->getKey())
            ->where('is_active', true)
            ->where('locale', app()->getLocale())
            ->orderBy('order')
            ->orderBy('title');
    }

    protected static function applyTaxonomyTermFilter(Builder $query, Taxonomy $taxonomy, string $termId): Builder
    {
        return $query->whereHas('terms', fn (Builder $termQuery) => $termQuery
            ->whereKey($termId)
            ->where('taxonomy_id', $taxonomy->getKey()));
    }
}

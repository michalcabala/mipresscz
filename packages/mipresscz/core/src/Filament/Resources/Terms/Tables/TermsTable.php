<?php

namespace MiPressCz\Core\Filament\Resources\Terms\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use MiPressCz\Core\Filament\Resources\Terms\TermResource;
use MiPressCz\Core\Models\Taxonomy;

class TermsTable
{
    public static function configure(Table $table): Table
    {
        $scopedTaxonomy = TermResource::getScopedTaxonomy();
        $hasScopedTaxonomy = $scopedTaxonomy !== null;
        $showParentColumn = ! $hasScopedTaxonomy || $scopedTaxonomy->is_hierarchical;

        return $table
            ->columns([
                TextColumn::make('locale')
                    ->label(__('content.entry_fields.locale'))
                    ->badge()
                    ->sortable()
                    ->visible(fn (): bool => locales()->isMultilingual()),
                TextColumn::make('title')
                    ->label(__('content.term_fields.title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label(__('content.term_fields.slug'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('taxonomy.title')
                    ->label(__('content.term_fields.taxonomy'))
                    ->sortable()
                    ->visible(! $hasScopedTaxonomy),
                TextColumn::make('parent.title')
                    ->label(__('content.term_fields.parent'))
                    ->placeholder('—')
                    ->visible($showParentColumn),
                TextColumn::make('order')
                    ->label(__('content.term_fields.order'))
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('content.collection_fields.is_active'))
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('taxonomy_id')
                    ->label(__('content.term_fields.taxonomy'))
                    ->options(Taxonomy::query()->orderBy('title')->pluck('title', 'id'))
                    ->visible(! $hasScopedTaxonomy),
                SelectFilter::make('locale')
                    ->label(__('content.entry_fields.locale'))
                    ->options(fn () => locales()->toSelectOptions())
                    ->visible(fn (): bool => locales()->isMultilingual()),
            ])
            ->defaultSort('order');
    }
}

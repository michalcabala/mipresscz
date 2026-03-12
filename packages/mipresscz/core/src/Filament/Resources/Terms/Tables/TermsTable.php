<?php

namespace MiPressCz\Core\Filament\Resources\Terms\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use MiPressCz\Core\Models\Taxonomy;

class TermsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('locale')
                    ->label(__('content.entry_fields.locale'))
                    ->badge()
                    ->sortable(),
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
                    ->sortable(),
                TextColumn::make('parent.title')
                    ->label(__('content.term_fields.parent'))
                    ->placeholder('—'),
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
                    ->options(Taxonomy::query()->orderBy('title')->pluck('title', 'id')),
                SelectFilter::make('locale')
                    ->label(__('content.entry_fields.locale'))
                    ->options(fn () => locales()->toSelectOptions()),
            ])
            ->defaultSort('order');
    }
}

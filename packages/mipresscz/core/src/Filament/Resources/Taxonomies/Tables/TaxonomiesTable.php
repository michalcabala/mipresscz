<?php

namespace MiPressCz\Core\Filament\Resources\Taxonomies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TaxonomiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('content.taxonomy_fields.title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('handle')
                    ->label(__('content.taxonomy_fields.handle'))
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                TextColumn::make('terms_count')
                    ->label(__('content.terms.plural_label'))
                    ->counts('terms')
                    ->sortable(),
                IconColumn::make('is_hierarchical')
                    ->label(__('content.taxonomy_fields.is_hierarchical'))
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label(__('content.taxonomy_fields.is_active'))
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

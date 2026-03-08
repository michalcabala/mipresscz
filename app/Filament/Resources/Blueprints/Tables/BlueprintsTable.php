<?php

namespace App\Filament\Resources\Blueprints\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BlueprintsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('content.blueprint_fields.title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('handle')
                    ->label(__('content.blueprint_fields.handle'))
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                TextColumn::make('collection.title')
                    ->label(__('content.blueprint_fields.collection'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('entries_count')
                    ->label(__('content.entries.plural_label'))
                    ->counts('entries')
                    ->sortable(),
                IconColumn::make('is_default')
                    ->label(__('content.blueprint_fields.is_default'))
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label(__('content.collection_fields.is_active'))
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label(__('content.collection_fields.title'))
                    ->isoDateTime('LLL')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('collection_id')
                    ->label(__('content.blueprint_fields.collection'))
                    ->relationship('collection', 'title'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('collection_id');
    }
}

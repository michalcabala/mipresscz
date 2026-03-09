<?php

namespace MiPressCz\Core\Filament\Resources\Collections\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CollectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('content.collection_fields.title'))
                    ->icon(fn ($record) => $record->icon)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('handle')
                    ->label(__('content.collection_fields.handle'))
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                TextColumn::make('entries_count')
                    ->label(__('content.entries.plural_label'))
                    ->counts('entries')
                    ->sortable(),
                TextColumn::make('blueprints_count')
                    ->label(__('content.blueprints.plural_label'))
                    ->counts('blueprints')
                    ->sortable(),
                IconColumn::make('is_tree')
                    ->label(__('content.collection_fields.is_tree'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
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

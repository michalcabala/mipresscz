<?php

namespace App\Filament\Resources\Blocks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BlocksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('content.block_fields.name'))
                    ->icon(fn ($record) => $record->icon)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('handle')
                    ->label(__('content.block_fields.handle'))
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                TextColumn::make('category')
                    ->label('Kategorie')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label(__('content.block_fields.is_active'))
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

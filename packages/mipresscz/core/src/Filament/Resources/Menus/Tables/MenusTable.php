<?php

namespace MiPressCz\Core\Filament\Resources\Menus\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MenusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('content.menu_fields.title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('handle')
                    ->label(__('content.menu_fields.handle'))
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                TextColumn::make('location')
                    ->label(__('content.menu_fields.location'))
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (?string $state): string => $state
                        ? __('content.menu_locations.'.$state)
                        : '—'),

                TextColumn::make('items_count')
                    ->label(__('content.menu_item_fields.title'))
                    ->counts('items')
                    ->sortable(),
            ])
            ->filters([])
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

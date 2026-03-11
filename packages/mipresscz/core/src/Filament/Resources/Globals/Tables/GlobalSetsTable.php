<?php

namespace MiPressCz\Core\Filament\Resources\Globals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GlobalSetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('content.global_fields.title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('handle')
                    ->label(__('content.global_fields.handle'))
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                TextColumn::make('locale')
                    ->label(__('content.entry_fields.locale'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __("content.locales.{$state}"))
                    ->color('info'),
                TextColumn::make('updated_at')
                    ->label('Upraveno')
                    ->isoDateTime('LLL')
                    ->sortable(),
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

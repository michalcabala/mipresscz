<?php

namespace App\Filament\Resources\Entries\Tables;

use App\Enums\EntryStatus;
use App\Filament\Resources\Entries\EntryResource;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EntriesTable
{
    public static function configure(Table $table): Table
    {
        $hasCollection = EntryResource::getCollectionHandle() !== null;

        return $table
            ->columns([
                CuratorColumn::make('featuredImage')
                    ->label(__('content.entry_fields.featured_image'))
                    ->size(48)
                    ->toggleable(),
                TextColumn::make('title')
                    ->label(__('content.entry_fields.title'))
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
                TextColumn::make('locale')
                    ->label(__('content.entry_fields.locale'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __("content.locales.{$state}"))
                    ->color('info'),
                TextColumn::make('author.name')
                    ->label(__('content.entry_fields.author'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('published_at')
                    ->label(__('content.entry_fields.published_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Upraveno')
                    ->isoDateTime('LLL')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('published_at', 'desc')
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
                    ->options([
                        'cs' => __('content.locales.cs'),
                        'en' => __('content.locales.en'),
                    ]),
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

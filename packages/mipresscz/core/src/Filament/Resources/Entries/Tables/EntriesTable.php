<?php

namespace MiPressCz\Core\Filament\Resources\Entries\Tables;

use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Filament\Resources\Entries\EntryResource;
use MiPressCz\Core\Models\Entry;

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
                    ->stacked(),
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
                    ->options(fn (): array => locales()->toSelectOptions())
                    ->query(fn (Builder $query, array $data): Builder => blank($data['value'])
                        ? $query
                        : $query->where(fn (Builder $q) => $q
                            ->where('locale', $data['value'])
                            ->orWhereHas('translations', fn (Builder $tq) => $tq->where('locale', $data['value'])))
                    ),
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
}

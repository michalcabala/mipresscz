<?php

namespace App\Filament\Resources\Entries\Tables;

use App\Enums\EntryStatus;
use App\Filament\Resources\Entries\EntryResource;
use App\Models\Entry;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                    ->html()
                    ->state(function (Entry $record): string {
                        $flagMap = [
                            'cs' => asset('assets/flags/CZ.svg'),
                            'en' => asset('assets/flags/GB-UKM.svg'),
                        ];

                        return collect([$record->locale])
                            ->merge($record->translations->pluck('locale'))
                            ->unique()
                            ->sort()
                            ->map(fn (string $locale): string => isset($flagMap[$locale])
                                ? '<img src="'.e($flagMap[$locale]).'" alt="'.e($locale).'" title="'.e(strtoupper($locale)).'" style="display:inline-block;width:1.35rem;height:1rem;border-radius:2px;margin-right:3px;" />'
                                : '<span class="text-xs font-mono">'.e(strtoupper($locale)).'</span>')
                            ->implode('');
                    }),
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
                    ])
                    ->query(fn (Builder $query, array $data): Builder => blank($data['value'])
                        ? $query
                        : $query->where(fn (Builder $q) => $q
                            ->where('locale', $data['value'])
                            ->orWhereHas('translations', fn (Builder $tq) => $tq->where('locale', $data['value'])))
                    ),
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

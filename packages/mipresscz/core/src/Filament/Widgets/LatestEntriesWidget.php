<?php

namespace MiPressCz\Core\Filament\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use MiPressCz\Core\Models\Entry;

class LatestEntriesWidget extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('content.stats.latest_entries'))
            ->query(
                Entry::query()
                    ->with(['collection', 'author'])
                    ->latest('updated_at')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('title')
                    ->label(__('content.entry_fields.title'))
                    ->limit(60)
                    ->searchable(),

                TextColumn::make('collection.name')
                    ->label(__('content.entry_fields.collection'))
                    ->badge()
                    ->color('gray'),

                TextColumn::make('status')
                    ->label(__('content.entry_fields.status'))
                    ->badge()
                    ->color(fn (Entry $record): string => match ($record->status) {
                        \MiPressCz\Core\Enums\EntryStatus::Published => 'success',
                        \MiPressCz\Core\Enums\EntryStatus::Draft => 'warning',
                        \MiPressCz\Core\Enums\EntryStatus::Scheduled => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (Entry $record): string => $record->status->label()),

                TextColumn::make('author.name')
                    ->label(__('content.entry_fields.author'))
                    ->default('—'),

                TextColumn::make('updated_at')
                    ->label(__('content.entry_fields.updated_at'))
                    ->since()
                    ->sortable(),
            ])
            ->paginated(false);
    }
}

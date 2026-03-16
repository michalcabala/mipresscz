<?php

namespace MiPressCz\Core\Filament\Resources\Entries\RelationManagers;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\Revision;

class RevisionsRelationManager extends RelationManager
{
    protected static string $relationship = 'revisions';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord instanceof Entry
            && (bool) $ownerRecord->collection?->revisions_enabled;
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('user')->latest('created_at'))
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('content.revision_fields.created_at'))
                    ->since()
                    ->sortable(),
                TextColumn::make('action')
                    ->label(__('content.revision_fields.action'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __('content.revision_fields.action_'.$state)),
                TextColumn::make('status')
                    ->label(__('content.entry_fields.status'))
                    ->badge()
                    ->icon(fn (string $state): string => EntryStatus::from($state)->icon())
                    ->color(fn (string $state): string => EntryStatus::from($state)->color())
                    ->formatStateUsing(fn (string $state): string => EntryStatus::from($state)->getLabel()),
                TextColumn::make('title')
                    ->label(__('content.entry_fields.title'))
                    ->wrap()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label(__('content.revision_fields.user'))
                    ->placeholder('-')
                    ->toggleable(),
                IconColumn::make('is_current')
                    ->label(__('content.revision_fields.is_current'))
                    ->boolean(),
            ])
            ->recordActions([
                Action::make('view')
                    ->label(__('content.actions.view_revision'))
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalSubmitAction(false)
                    ->modalHeading(__('content.actions.view_revision'))
                    ->modalContent(fn (Revision $record): View => view('mipresscz-core::filament.entries.revisions.modal-content', [
                        'revision' => $record,
                    ])),
                Action::make('restore')
                    ->label(__('content.actions.restore_revision'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->hidden(fn (Revision $record): bool => $record->is_current || ! auth()->user()?->can('update', $this->getOwnerRecord()))
                    ->action(function (Revision $record): void {
                        /** @var Entry $ownerRecord */
                        $ownerRecord = $this->getOwnerRecord();
                        $loadsWorkingCopy = $this->usesWorkingCopy($ownerRecord);

                        $ownerRecord->restoreRevision($record, auth()->user());

                        Notification::make()
                            ->title($loadsWorkingCopy
                                ? __('content.messages.revision_loaded_to_working_copy')
                                : __('content.messages.revision_restored'))
                            ->success()
                            ->send();

                        $this->redirect(request()->fullUrl());
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('content.revisions.plural_label');
    }

    protected function usesWorkingCopy(Entry $entry): bool
    {
        return $entry->status === EntryStatus::Published
            && $entry->collection?->revisions_enabled;
    }
}

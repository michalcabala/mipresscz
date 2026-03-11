<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('users.fields.name'))
                    ->description(fn ($record) => $record->email)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role')
                    ->label(__('users.fields.role'))
                    ->formatStateUsing(fn (UserRole $state): string => $state->label())
                    ->icon(fn (UserRole $state): string => $state->icon())
                    ->color(fn (UserRole $state): string => $state->color())
                    ->badge()
                    ->sortable(),
                TextColumn::make('email_verified_at')
                    ->label(__('users.fields.email_verified_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('deleted_at')
                    ->label(__('users.fields.deleted_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('users.fields.created_at'))
                    ->isoDateTime('LLL')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('users.fields.updated_at'))
                    ->isoDateTime('LLL')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->hidden(fn (User $record): bool => $record->trashed()),
                DeleteAction::make()
                    ->hidden(fn (User $record): bool => $record->trashed())
                    ->before(function (User $record, DeleteAction $action): void {
                        if ($record->id === Auth::id()) {
                            Notification::make()
                                ->title(__('users.messages.cannot_delete_self'))
                                ->danger()
                                ->send();
                            $action->cancel();
                        }
                    }),
                RestoreAction::make()
                    ->visible(fn (User $record): bool => $record->trashed()),
                ForceDeleteAction::make()
                    ->visible(fn (User $record): bool => $record->trashed())
                    ->before(function (User $record, ForceDeleteAction $action): void {
                        if ($record->id === Auth::id()) {
                            Notification::make()
                                ->title(__('users.messages.cannot_delete_self'))
                                ->danger()
                                ->send();
                            $action->cancel();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (DeleteBulkAction $action, \Illuminate\Support\Collection $records): void {
                            $records
                                ->filter(fn (User $record): bool => $record->id !== Auth::id())
                                ->each(fn (User $record) => $record->delete());
                        }),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make()
                        ->action(function (ForceDeleteBulkAction $action, \Illuminate\Support\Collection $records): void {
                            $records
                                ->filter(fn (User $record): bool => $record->id !== Auth::id())
                                ->each(fn (User $record) => $record->forceDelete());
                        }),
                ]),
            ]);
    }
}

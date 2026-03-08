<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('users.fields.name'))
                    ->required(),
                TextInput::make('email')
                    ->label(__('users.fields.email'))
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->label(__('users.fields.password'))
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state)),
                Select::make('role')
                    ->label(__('users.fields.role'))
                    ->options(UserRole::toFilamentSelect())
                    ->default(UserRole::Contributor->value)
                    ->required(),
                DateTimePicker::make('email_verified_at')
                    ->label(__('users.fields.email_verified_at')),
            ]);
    }
}

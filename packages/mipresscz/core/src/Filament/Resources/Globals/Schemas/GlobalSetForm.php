<?php

namespace MiPressCz\Core\Filament\Resources\Globals\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GlobalSetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label(__('content.global_fields.title'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, callable $set, string $operation) {
                        if ($operation === 'create') {
                            $set('handle', \Illuminate\Support\Str::slug($state, '_'));
                        }
                    }),
                TextInput::make('handle')
                    ->label(__('content.global_fields.handle'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->alphaDash(),
                Select::make('locale')
                    ->label(__('content.entry_fields.locale'))
                    ->options([
                        'cs' => __('content.locales.cs'),
                        'en' => __('content.locales.en'),
                    ])
                    ->default('cs')
                    ->required(),
                KeyValue::make('data')
                    ->label(__('content.global_fields.data'))
                    ->columnSpanFull(),
            ]);
    }
}

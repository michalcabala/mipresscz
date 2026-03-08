<?php

namespace App\Filament\Resources\Blocks\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BlockForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label(__('content.block_fields.name'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $state, callable $set, string $operation) {
                        if ($operation === 'create') {
                            $set('handle', \Illuminate\Support\Str::slug($state, '_'));
                        }
                    }),
                TextInput::make('handle')
                    ->label(__('content.block_fields.handle'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->alphaDash(),
                Textarea::make('description')
                    ->label(__('content.block_fields.description')),
                TextInput::make('icon')
                    ->label(__('content.block_fields.icon'))
                    ->placeholder('fal-cube'),
                Toggle::make('is_active')
                    ->label(__('content.block_fields.is_active'))
                    ->default(true),
                KeyValue::make('fields')
                    ->label(__('content.block_fields.fields'))
                    ->columnSpanFull(),
            ]);
    }
}

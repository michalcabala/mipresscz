<?php

namespace MiPressCz\Core\Filament\Resources\Taxonomies\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TaxonomyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label(__('content.taxonomy_fields.title'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $state, callable $set, string $operation) {
                        if ($operation === 'create') {
                            $set('handle', \Illuminate\Support\Str::slug($state, '_'));
                        }
                    }),
                TextInput::make('handle')
                    ->label(__('content.taxonomy_fields.handle'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->alphaDash(),
                Textarea::make('description')
                    ->label(__('content.taxonomy_fields.description')),
                Toggle::make('is_hierarchical')
                    ->label(__('content.taxonomy_fields.is_hierarchical'))
                    ->default(false),
                Toggle::make('is_active')
                    ->label(__('content.taxonomy_fields.is_active'))
                    ->default(true),
                Select::make('collections')
                    ->label(__('content.taxonomy_fields.collections'))
                    ->relationship('collections', 'title')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }
}

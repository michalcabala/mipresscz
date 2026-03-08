<?php

namespace App\Filament\Resources\Collections\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BlueprintsRelationManager extends RelationManager
{
    protected static string $relationship = 'blueprints';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label(__('content.blueprint_fields.title'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $state, callable $set, string $operation) {
                        if ($operation === 'create') {
                            $set('handle', \Illuminate\Support\Str::slug($state, '_'));
                        }
                    }),
                TextInput::make('handle')
                    ->label(__('content.blueprint_fields.handle'))
                    ->required()
                    ->maxLength(255)
                    ->alphaDash(),
                Toggle::make('is_default')
                    ->label(__('content.blueprint_fields.is_default'))
                    ->default(false),
                Toggle::make('is_active')
                    ->label(__('content.collection_fields.is_active'))
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('content.blueprint_fields.title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('handle')
                    ->label(__('content.blueprint_fields.handle'))
                    ->searchable(),
                IconColumn::make('is_default')
                    ->label(__('content.blueprint_fields.is_default'))
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label(__('content.collection_fields.is_active'))
                    ->boolean(),
            ])
            ->defaultSort('order');
    }

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('content.blueprints.plural_label');
    }
}

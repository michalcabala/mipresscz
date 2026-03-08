<?php

namespace App\Filament\Resources\Collections\Schemas;

use App\Enums\DateBehavior;
use App\Enums\DefaultStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CollectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('content.collection_fields.title'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label(__('content.collection_fields.title'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $state, callable $set, string $operation) {
                                if ($operation === 'create') {
                                    $set('handle', \Illuminate\Support\Str::slug($state, '_'));
                                }
                            }),
                        TextInput::make('handle')
                            ->label(__('content.collection_fields.handle'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->alphaDash(),
                        Textarea::make('description')
                            ->label(__('content.collection_fields.description'))
                            ->columnSpanFull(),
                    ]),
                Section::make(__('content.collection_fields.settings'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('route_template')
                            ->label(__('content.collection_fields.route_template'))
                            ->placeholder('/{slug}'),
                        TextInput::make('icon')
                            ->label(__('content.collection_fields.icon'))
                            ->placeholder('fal-file-lines'),
                        Select::make('sort_field')
                            ->label(__('content.collection_fields.sort_field'))
                            ->options([
                                'order' => 'Order',
                                'title' => 'Title',
                                'published_at' => 'Published at',
                                'created_at' => 'Created at',
                            ])
                            ->default('order'),
                        Select::make('sort_direction')
                            ->label(__('content.collection_fields.sort_direction'))
                            ->options([
                                'asc' => __('content.sort_directions.asc'),
                                'desc' => __('content.sort_directions.desc'),
                            ])
                            ->default('asc'),
                        Select::make('date_behavior')
                            ->label(__('content.collection_fields.date_behavior'))
                            ->options(DateBehavior::class)
                            ->default(DateBehavior::None),
                        Select::make('default_status')
                            ->label(__('content.collection_fields.default_status'))
                            ->options(DefaultStatus::class)
                            ->default(DefaultStatus::Draft),
                        Toggle::make('is_tree')
                            ->label(__('content.collection_fields.is_tree'))
                            ->default(false),
                        Toggle::make('revisions_enabled')
                            ->label(__('content.collection_fields.revisions_enabled'))
                            ->default(false),
                        Toggle::make('is_active')
                            ->label(__('content.collection_fields.is_active'))
                            ->default(true),
                    ]),
            ]);
    }
}

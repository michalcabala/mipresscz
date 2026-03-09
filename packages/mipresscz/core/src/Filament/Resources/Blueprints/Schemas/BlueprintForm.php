<?php

namespace MiPressCz\Core\Filament\Resources\Blueprints\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use MiPressCz\Core\Models\Collection;

class BlueprintForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('content.blueprint_fields.title'))
                    ->columns(2)
                    ->schema([
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
                        Select::make('collection_id')
                            ->label(__('content.blueprint_fields.collection'))
                            ->options(Collection::query()->orderBy('title')->pluck('title', 'id'))
                            ->searchable()
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make(__('content.collection_fields.settings'))
                    ->columns(3)
                    ->schema([
                        TextInput::make('icon')
                            ->label(__('content.collection_fields.icon'))
                            ->placeholder('fal-file-lines'),
                        TextInput::make('order')
                            ->label(__('content.entry_fields.order'))
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_default')
                            ->label(__('content.blueprint_fields.is_default'))
                            ->default(false),
                        Toggle::make('is_active')
                            ->label(__('content.collection_fields.is_active'))
                            ->default(true),
                    ]),

                Section::make(__('content.blueprint_fields.fields'))
                    ->schema([
                        Repeater::make('fields')
                            ->label('')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('handle')
                                            ->label(__('content.blueprint_fields.handle'))
                                            ->required()
                                            ->alphaDash(),
                                        Select::make('type')
                                            ->label(__('content.field_types.label'))
                                            ->options([
                                                'text' => __('content.field_types.text'),
                                                'textarea' => __('content.field_types.textarea'),
                                                'rich_editor' => __('content.field_types.rich_editor'),
                                                'mason' => __('content.field_types.mason'),
                                                'number' => __('content.field_types.number'),
                                                'select' => __('content.field_types.select'),
                                                'toggle' => __('content.field_types.toggle'),
                                                'curator' => __('content.field_types.curator'),
                                                'entries' => __('content.field_types.entries'),
                                            ])
                                            ->required()
                                            ->default('text'),
                                    ]),
                                TextInput::make('display')
                                    ->label(__('content.collection_fields.title'))
                                    ->required()
                                    ->columnSpanFull(),
                                Textarea::make('instructions')
                                    ->label(__('content.field_config.instructions'))
                                    ->rows(2)
                                    ->columnSpanFull(),
                                Grid::make(3)
                                    ->schema([
                                        Select::make('section')
                                            ->label(__('content.field_config.section'))
                                            ->options([
                                                'main' => __('content.field_config.section_main'),
                                                'sidebar' => __('content.field_config.section_sidebar'),
                                            ])
                                            ->default('main'),
                                        Select::make('width')
                                            ->label(__('content.field_config.width'))
                                            ->options([
                                                50 => __('content.field_config.width_half'),
                                                100 => __('content.field_config.width_full'),
                                            ])
                                            ->default(100),
                                        TextInput::make('order')
                                            ->label(__('content.entry_fields.order'))
                                            ->numeric()
                                            ->default(0),
                                    ]),
                                Grid::make(2)
                                    ->schema([
                                        Toggle::make('required')
                                            ->label(__('content.field_config.required'))
                                            ->default(false),
                                        Toggle::make('translatable')
                                            ->label(__('content.field_config.translatable'))
                                            ->default(false),
                                    ]),
                                KeyValue::make('config')
                                    ->label(__('content.field_config.config'))
                                    ->columnSpanFull(),
                                Hidden::make('conditions')
                                    ->default([]),
                            ])
                            ->defaultItems(0)
                            ->reorderable()
                            ->collapsible()
                            ->cloneable()
                            ->addActionLabel(__('content.blueprint_fields.add_field'))
                            ->itemLabel(fn (array $state): string => ($state['display'] ?? '') !== ''
                                ? ($state['display'].' ('.(($state['handle'] ?? '') !== '' ? $state['handle'] : '?').')')
                                : (($state['handle'] ?? '') !== '' ? $state['handle'] : '...')),
                    ]),
            ]);
    }
}

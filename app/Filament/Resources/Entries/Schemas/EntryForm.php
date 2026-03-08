<?php

namespace App\Filament\Resources\Entries\Schemas;

use App\Enums\EntryStatus;
use App\Filament\Resources\Entries\EntryResource;
use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class EntryForm
{
    public static function configure(Schema $schema): Schema
    {
        $collectionHandle = EntryResource::getCollectionHandle();

        return $schema
            ->components([
                Grid::make(3)->schema([
                    Section::make(__('content.entry_fields.content'))
                        ->columnSpan(2)
                        ->schema([
                            TextInput::make('title')
                                ->label(__('content.entry_fields.title'))
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (string $state, callable $set, string $operation) {
                                    if ($operation === 'create') {
                                        $set('slug', \Illuminate\Support\Str::slug($state));
                                    }
                                }),
                            TextInput::make('slug')
                                ->label(__('content.entry_fields.slug'))
                                ->required()
                                ->maxLength(255),

                            ...static::dynamicFields(),
                        ]),
                    Section::make(__('content.entry_fields.status'))
                        ->columnSpan(1)
                        ->schema([
                            Select::make('collection_id')
                                ->label(__('content.entry_fields.collection'))
                                ->relationship('collection', 'title')
                                ->required()
                                ->live()
                                ->default(fn () => $collectionHandle
                                    ? Collection::query()->where('handle', $collectionHandle)->value('id')
                                    : null)
                                ->disabled(fn () => (bool) $collectionHandle)
                                ->dehydrated()
                                ->afterStateUpdated(fn (callable $set) => $set('blueprint_id', null)),
                            Select::make('blueprint_id')
                                ->label(__('content.entry_fields.blueprint'))
                                ->options(function (Get $get) {
                                    $collectionId = $get('collection_id');
                                    if (! $collectionId) {
                                        return [];
                                    }

                                    return Blueprint::query()
                                        ->where('collection_id', $collectionId)
                                        ->pluck('title', 'id');
                                })
                                ->required()
                                ->live(),
                            Select::make('status')
                                ->label(__('content.entry_fields.status'))
                                ->options(EntryStatus::class)
                                ->default(EntryStatus::Draft)
                                ->required(),
                            Select::make('locale')
                                ->label(__('content.entry_fields.locale'))
                                ->options([
                                    'cs' => __('content.locales.cs'),
                                    'en' => __('content.locales.en'),
                                ])
                                ->default('cs')
                                ->required(),
                            DateTimePicker::make('published_at')
                                ->label(__('content.entry_fields.published_at')),
                            Select::make('author_id')
                                ->label(__('content.entry_fields.author'))
                                ->relationship('author', 'name')
                                ->default(fn () => auth()->id()),
                            Select::make('parent_id')
                                ->label(__('content.entry_fields.parent'))
                                ->options(function (Get $get, ?Entry $record) {
                                    $collectionId = $get('collection_id');
                                    if (! $collectionId) {
                                        return [];
                                    }
                                    $collection = Collection::find($collectionId);
                                    if (! $collection?->is_tree) {
                                        return [];
                                    }

                                    return Entry::query()
                                        ->where('collection_id', $collectionId)
                                        ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                        ->pluck('title', 'id');
                                })
                                ->visible(function (Get $get) {
                                    $collectionId = $get('collection_id');
                                    if (! $collectionId) {
                                        return false;
                                    }

                                    return Collection::find($collectionId)?->is_tree ?? false;
                                }),
                            Toggle::make('is_pinned')
                                ->label('Připnuto'),
                            TextInput::make('order')
                                ->label(__('content.entry_fields.order'))
                                ->numeric()
                                ->default(0),
                        ]),
                ]),
            ]);
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected static function dynamicFields(): array
    {
        return [
            KeyValue::make('data')
                ->label(__('content.entry_fields.data'))
                ->columnSpanFull()
                ->addActionLabel(__('content.actions.create_entry')),
        ];
    }
}

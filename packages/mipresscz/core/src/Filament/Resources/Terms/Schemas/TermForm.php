<?php

namespace MiPressCz\Core\Filament\Resources\Terms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use MiPressCz\Core\Models\Taxonomy;
use MiPressCz\Core\Models\Term;

class TermForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('content.term_fields.title'))
                    ->columns(2)
                    ->schema([
                        Select::make('taxonomy_id')
                            ->label(__('content.term_fields.taxonomy'))
                            ->options(Taxonomy::query()->where('is_active', true)->orderBy('title')->pluck('title', 'id'))
                            ->searchable()
                            ->required()
                            ->live()
                            ->columnSpanFull(),
                        TextInput::make('title')
                            ->label(__('content.term_fields.title'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, callable $set, string $operation) {
                                if ($operation === 'create') {
                                    $set('slug', \Illuminate\Support\Str::slug($state));
                                }
                            }),
                        TextInput::make('slug')
                            ->label(__('content.term_fields.slug'))
                            ->required()
                            ->maxLength(255)
                            ->alphaDash(),
                        Select::make('parent_id')
                            ->label(__('content.term_fields.parent'))
                            ->options(function (Get $get, ?Term $record): array {
                                $taxonomyId = $get('taxonomy_id');

                                if (! $taxonomyId) {
                                    return [];
                                }

                                return Term::query()
                                    ->where('taxonomy_id', $taxonomyId)
                                    ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                    ->orderBy('title')
                                    ->pluck('title', 'id')
                                    ->all();
                            })
                            ->searchable()
                            ->placeholder('—'),
                        TextInput::make('order')
                            ->label(__('content.term_fields.order'))
                            ->numeric()
                            ->default(0),
                        Textarea::make('description')
                            ->label(__('content.taxonomy_fields.description'))
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label(__('content.collection_fields.is_active'))
                            ->default(true),
                    ]),
            ]);
    }
}

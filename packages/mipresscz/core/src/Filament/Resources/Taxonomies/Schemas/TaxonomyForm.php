<?php

namespace MiPressCz\Core\Filament\Resources\Taxonomies\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use MiPressCz\Core\Models\Locale;

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
                    ->afterStateUpdated(function (?string $state, callable $set, string $operation) {
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
                Section::make(__('content.taxonomy_fields.translations'))
                    ->description(__('content.taxonomy_fields.translations_hint'))
                    ->collapsed()
                    ->schema([
                        Tabs::make('translations')
                            ->contained(false)
                            ->tabs(static::buildTranslationTabs()),
                    ]),
            ]);
    }

    /** @return array<int, Tab> */
    private static function buildTranslationTabs(): array
    {
        return locales()->getActive()
            ->map(fn (Locale $locale): Tab => Tab::make($locale->code)
                ->label($locale->native_name ?? $locale->name)
                ->schema([
                    TextInput::make("translations.{$locale->code}.title")
                        ->label(__('content.taxonomy_fields.title'))
                        ->maxLength(255),
                    Textarea::make("translations.{$locale->code}.description")
                        ->label(__('content.taxonomy_fields.description')),
                ])
            )
            ->values()
            ->all();
    }
}

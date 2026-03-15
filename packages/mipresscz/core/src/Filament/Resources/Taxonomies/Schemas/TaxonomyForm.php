<?php

namespace MiPressCz\Core\Filament\Resources\Taxonomies\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Locale;
use MiPressCz\Core\Models\Taxonomy;

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
                Select::make('collection_id')
                    ->label(__('content.entry_fields.collection'))
                    ->options(fn (): array => Collection::query()
                        ->where('is_active', true)
                        ->orderBy('title')
                        ->pluck('title', 'id')
                        ->all())
                    ->preload()
                    ->searchable()
                    ->afterStateHydrated(function (Select $component, ?Taxonomy $record): void {
                        if (! $record) {
                            return;
                        }

                        $component->state($record->collections()->value('collections.id'));
                    }),
                Section::make(__('content.taxonomy_fields.translations'))
                    ->description(__('content.taxonomy_fields.translations_hint'))
                    ->collapsed()
                    ->schema(static::buildTranslationSwitcher()),
            ]);
    }

    /** @return array<int, \Filament\Schemas\Components\Component|\Filament\Forms\Components\Field> */
    private static function buildTranslationSwitcher(): array
    {
        $locales = locales()->getActive();
        $default = locales()->getDefaultCode();

        $picker = Select::make('_locale_tab')
            ->label(__('content.taxonomy_fields.language'))
            ->options($locales->mapWithKeys(fn (Locale $l): array => [$l->code => $l->native_name ?? $l->name])->all())
            ->formatStateUsing(fn (?string $state): string => $state ?: $default)
            ->live()
            ->dehydrated(false)
            ->selectablePlaceholder(false);

        $groups = $locales
            ->map(fn (Locale $locale): Group => Group::make([
                TextInput::make("translations.{$locale->code}.title")
                    ->label(__('content.taxonomy_fields.title'))
                    ->maxLength(255),
                Textarea::make("translations.{$locale->code}.description")
                    ->label(__('content.taxonomy_fields.description')),
            ])->visible(fn (Get $get): bool => ($get('_locale_tab') ?: $default) === $locale->code))
            ->values()
            ->all();

        return [$picker, ...$groups];
    }
}

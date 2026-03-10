<?php

namespace MiPressCz\Core\Filament\Resources\Entries\Schemas;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Mason\Mason;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;

class EntryForm
{
    /** @var array<int, class-string<\Awcodes\Mason\Brick>> */
    public static array $brickClasses = [];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ── Main content area (2/3 width) ──
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('title')
                                    ->label(__('content.entry_fields.title'))
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (?string $state, callable $set, string $operation) {
                                        if ($operation === 'create') {
                                            $set('slug', \Illuminate\Support\Str::slug($state));
                                        }
                                    })
                                    ->autofocus(),
                                TextInput::make('slug')
                                    ->label(__('content.entry_fields.slug'))
                                    ->required()
                                    ->maxLength(255)
                                    ->prefix('/'),

                                // Mason is always the content editor
                                Mason::make('content')
                                    ->label(__('content.entry_fields.content'))
                                    ->bricks(static::$brickClasses)
                                    ->columnSpanFull(),
                            ]),

                        // Dynamic main fields from blueprint
                        Section::make(__('content.entry_fields.extra_fields'))
                            ->schema(fn (?Entry $record): array => static::buildMainFieldsForBlueprint(
                                $record?->blueprint_id ?? static::resolveDefaultBlueprintId()
                            ))
                            ->visible(fn (?Entry $record): bool => count(
                                static::buildMainFieldsForBlueprint(
                                    $record?->blueprint_id ?? static::resolveDefaultBlueprintId()
                                )
                            ) > 0)
                            ->collapsible(),
                    ])
                    ->columnSpan(['lg' => 2]),

                // ── Sidebar (1/3 width) ──
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                // Featured image
                                CuratorPicker::make('featured_image_id')
                                    ->label(__('content.entry_fields.featured_image'))
                                    ->relationship('featuredImage', 'id')
                                    ->constrained(true)
                                    ->lazyLoad(true),

                                Select::make('author_id')
                                    ->label(__('content.entry_fields.author'))
                                    ->relationship('author', 'name')
                                    ->default(fn () => auth()->id()),

                                DateTimePicker::make('published_at')
                                    ->label(__('content.entry_fields.published_at')),

                                // Tree hierarchy — only for tree collections
                                Select::make('parent_id')
                                    ->label(__('content.entry_fields.parent'))
                                    ->options(function (Get $get, ?Entry $record) {
                                        $collectionId = $get('collection_id');
                                        if (! $collectionId) {
                                            return [];
                                        }

                                        return Entry::query()
                                            ->where('collection_id', $collectionId)
                                            ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                            ->pluck('title', 'id');
                                    })
                                    ->visible(fn (?Entry $record): bool => $record?->collection?->is_tree ?? false),

                                Toggle::make('is_pinned')
                                    ->label(__('content.entry_fields.is_pinned')),
                            ]),

                        // Dynamic sidebar fields from blueprint
                        Section::make(__('content.entry_fields.metadata'))
                            ->schema(fn (?Entry $record): array => static::buildSidebarFieldsForBlueprint(
                                $record?->blueprint_id ?? static::resolveDefaultBlueprintId()
                            ))
                            ->visible(fn (?Entry $record): bool => count(
                                static::buildSidebarFieldsForBlueprint(
                                    $record?->blueprint_id ?? static::resolveDefaultBlueprintId()
                                )
                            ) > 0)
                            ->collapsible(),
                    ])
                    ->columnSpan(['lg' => 1]),

                // Hidden fields — auto-populated in Create/Edit pages
                Hidden::make('collection_id'),
                Hidden::make('blueprint_id'),
                Hidden::make('locale'),
                Hidden::make('status')
                    ->default(EntryStatus::Draft->value),
                Hidden::make('order')
                    ->default(0),
            ])
            ->columns(3);
    }

    /**
     * Determine the default blueprint ID for CreateRecord context (no $record yet).
     */
    protected static function resolveDefaultBlueprintId(): ?string
    {
        static $cache = [];
        $handle = \MiPressCz\Core\Filament\Resources\Entries\EntryResource::getCollectionHandle();

        if (! $handle) {
            return null;
        }

        if (array_key_exists($handle, $cache)) {
            return $cache[$handle];
        }

        $collection = Collection::query()
            ->where('handle', $handle)
            ->with('blueprints')
            ->first();

        return $cache[$handle] = $collection?->defaultBlueprint()?->id
            ?? $collection?->blueprints->first()?->id;
    }

    /**
     * Build typed Filament form components from a blueprint's fields for the given section.
     *
     * @param  array<int, array<string, mixed>>  $fields
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    protected static function buildFieldComponents(array $fields): array
    {
        $components = [];

        foreach ($fields as $field) {
            $handle = $field['handle'] ?? null;
            $type = $field['type'] ?? 'text';
            $label = $field['display'] ?? $handle;
            $required = $field['required'] ?? false;

            if (! $handle) {
                continue;
            }

            $name = "data.{$handle}";

            $component = match ($type) {
                'textarea' => Textarea::make($name)->label($label)->rows(4),
                'rich_editor' => RichEditor::make($name)->label($label),
                'curator', 'media' => CuratorPicker::make($name)->label($label)->constrained(true)->lazyLoad(true),
                'select' => Select::make($name)->label($label)->options($field['config']['options'] ?? []),
                'toggle' => Toggle::make($name)->label($label),
                'number' => TextInput::make($name)->label($label)->numeric(),
                'entries' => Select::make($name)
                    ->label($label)
                    ->multiple()
                    ->options(function () use ($field): array {
                        $collections = $field['config']['collections'] ?? [];
                        if (empty($collections)) {
                            return [];
                        }

                        return Entry::query()
                            ->whereHas('collection', fn ($q) => $q->whereIn('handle', $collections))
                            ->whereNull('origin_id')
                            ->limit(200)
                            ->pluck('title', 'id')
                            ->all();
                    }),
                default => TextInput::make($name)->label($label)->maxLength(255),
            };

            if ($required) {
                $component = $component->required();
            }

            $components[] = $component->columnSpanFull();
        }

        return $components;
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    protected static function resolveBlueprint(?string $blueprintId): ?Blueprint
    {
        static $cache = [];

        if (! $blueprintId) {
            return null;
        }

        return $cache[$blueprintId] ??= Blueprint::find($blueprintId);
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    protected static function buildMainFieldsForBlueprint(?string $blueprintId): array
    {
        $blueprint = static::resolveBlueprint($blueprintId);

        if (! $blueprint) {
            return [];
        }

        // Mason replaces rich content types — skip them to avoid duplication
        // Also skip featured_image — it's a fixed field in the sidebar
        $contentTypes = ['rich_editor', 'blocks', 'mason'];
        $alwaysFixed = ['featured_image'];

        $fields = collect($blueprint->getFieldsBySection('main'))
            ->reject(fn (array $f) => in_array($f['type'] ?? '', $contentTypes))
            ->reject(fn (array $f) => in_array($f['handle'] ?? '', $alwaysFixed))
            ->values()
            ->all();

        return static::buildFieldComponents($fields);
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    protected static function buildSidebarFieldsForBlueprint(?string $blueprintId): array
    {
        $blueprint = static::resolveBlueprint($blueprintId);

        if (! $blueprint) {
            return [];
        }

        // featured_image is always shown above — skip it from blueprint sidebar fields
        $fields = collect($blueprint->getFieldsBySection('sidebar'))
            ->reject(fn (array $f) => in_array($f['handle'] ?? '', ['featured_image']))
            ->values()
            ->all();

        return static::buildFieldComponents($fields);
    }
}

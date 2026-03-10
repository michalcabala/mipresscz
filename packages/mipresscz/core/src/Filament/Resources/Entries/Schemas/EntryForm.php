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

                                // Mason block editor — only when bricks are registered
                                Mason::make('content')
                                    ->label(__('content.entry_fields.content'))
                                    ->bricks(static::$brickClasses)
                                    ->columnSpanFull()
                                    ->visible(fn (): bool => count(static::$brickClasses) > 0),
                            ]),

                        // Dynamic main fields from blueprint
                        ...static::dynamicMainFields(),
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
                        ...static::dynamicSidebarFields(),
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
    protected static function dynamicMainFields(): array
    {
        return [
            Section::make(__('content.entry_fields.extra_fields'))
                ->schema(fn (Get $get): array => static::buildMainFieldsForBlueprint($get('blueprint_id')))
                ->visible(fn (Get $get): bool => (bool) $get('blueprint_id'))
                ->collapsible(),
        ];
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    protected static function dynamicSidebarFields(): array
    {
        return [
            Section::make(__('content.entry_fields.metadata'))
                ->schema(fn (Get $get): array => static::buildSidebarFieldsForBlueprint($get('blueprint_id')))
                ->visible(fn (Get $get): bool => (bool) $get('blueprint_id'))
                ->collapsible(),
        ];
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

        // Skip rich content types — they are replaced by Mason
        // Skip featured_image — it's a fixed field in the sidebar
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

        // Skip featured_image — it's a fixed field in the sidebar
        $fields = collect($blueprint->getFieldsBySection('sidebar'))
            ->reject(fn (array $f) => in_array($f['handle'] ?? '', ['featured_image']))
            ->values()
            ->all();

        return static::buildFieldComponents($fields);
    }
}

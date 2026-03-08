<?php

namespace App\Filament\Resources\Entries;

use App\Filament\Resources\Entries\Pages\CreateEntry;
use App\Filament\Resources\Entries\Pages\EditEntry;
use App\Filament\Resources\Entries\Pages\ListEntries;
use App\Filament\Resources\Entries\Schemas\EntryForm;
use App\Filament\Resources\Entries\Tables\EntriesTable;
use App\Models\Entry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class EntryResource extends Resource
{
    protected static ?string $model = Entry::class;

    protected static string|BackedEnum|null $navigationIcon = 'fal-file-lines';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 2;

    protected static ?string $configurationClass = EntryResourceConfiguration::class;

    public static function shouldRegisterNavigation(): bool
    {
        return static::hasConfiguration();
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('content.entries.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        if ($configuration = static::getConfiguration()) {
            if ($label = $configuration->getNavigationLabel()) {
                return $label;
            }
        }

        return __('content.entries.navigation_label');
    }

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        if ($configuration = static::getConfiguration()) {
            if ($icon = $configuration->getNavigationIcon()) {
                return $icon;
            }
        }

        return static::$navigationIcon;
    }

    public static function getNavigationSort(): ?int
    {
        if ($configuration = static::getConfiguration()) {
            if ($sort = $configuration->getNavigationSort()) {
                return $sort;
            }
        }

        return static::$navigationSort;
    }

    public static function getModelLabel(): string
    {
        return __('content.entries.label');
    }

    public static function getPluralModelLabel(): string
    {
        if ($configuration = static::getConfiguration()) {
            if ($label = $configuration->getNavigationLabel()) {
                return $label;
            }
        }

        return __('content.entries.plural_label');
    }

    public static function getCollectionHandle(): ?string
    {
        return static::getConfiguration()?->getCollectionHandle();
    }

    public static function form(Schema $schema): Schema
    {
        return EntryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EntriesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['collection', 'blueprint', 'author', 'featuredImage']);

        if ($handle = static::getCollectionHandle()) {
            $query->whereHas('collection', fn (Builder $q) => $q->where('handle', $handle));
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEntries::route('/'),
            'create' => CreateEntry::route('/create'),
            'edit' => EditEntry::route('/{record}/edit'),
        ];
    }
}

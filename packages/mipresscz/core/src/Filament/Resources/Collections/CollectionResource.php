<?php

namespace MiPressCz\Core\Filament\Resources\Collections;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use MiPressCz\Core\Filament\Resources\Collections\Pages\CreateCollection;
use MiPressCz\Core\Filament\Resources\Collections\Pages\EditCollection;
use MiPressCz\Core\Filament\Resources\Collections\Pages\ListCollections;
use MiPressCz\Core\Filament\Resources\Collections\Schemas\CollectionForm;
use MiPressCz\Core\Filament\Resources\Collections\Tables\CollectionsTable;
use MiPressCz\Core\Models\Collection;

class CollectionResource extends Resource
{
    protected static ?string $model = Collection::class;

    protected static string|BackedEnum|null $navigationIcon = 'far-layer-group';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('content.collections.navigation_group');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\BlueprintsRelationManager::class,
        ];
    }

    public static function getModelLabel(): string
    {
        return __('content.collections.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('content.collections.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('content.collections.navigation_label');
    }

    public static function form(Schema $schema): Schema
    {
        return CollectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CollectionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCollections::route('/'),
            'create' => CreateCollection::route('/create'),
            'edit' => EditCollection::route('/{record}/edit'),
        ];
    }
}

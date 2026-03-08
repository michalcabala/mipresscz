<?php

namespace App\Filament\Resources\Collections;

use App\Filament\Resources\Collections\Pages\CreateCollection;
use App\Filament\Resources\Collections\Pages\EditCollection;
use App\Filament\Resources\Collections\Pages\ListCollections;
use App\Filament\Resources\Collections\Schemas\CollectionForm;
use App\Filament\Resources\Collections\Tables\CollectionsTable;
use App\Models\Collection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CollectionResource extends Resource
{
    protected static ?string $model = Collection::class;

    protected static string|BackedEnum|null $navigationIcon = 'fal-layer-group';

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

<?php

namespace MiPressCz\Core\Filament\Resources\Blueprints;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use MiPressCz\Core\Filament\Resources\Blueprints\Pages\CreateBlueprint;
use MiPressCz\Core\Filament\Resources\Blueprints\Pages\EditBlueprint;
use MiPressCz\Core\Filament\Resources\Blueprints\Pages\ListBlueprints;
use MiPressCz\Core\Filament\Resources\Blueprints\Schemas\BlueprintForm;
use MiPressCz\Core\Filament\Resources\Blueprints\Tables\BlueprintsTable;
use MiPressCz\Core\Models\Blueprint;

class BlueprintResource extends Resource
{
    protected static ?string $model = Blueprint::class;

    protected static string|BackedEnum|null $navigationIcon = 'fal-puzzle-piece';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('content.blueprints.navigation_group');
    }

    public static function getNavigationParentItem(): ?string
    {
        return __('content.collections.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('content.blueprints.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('content.blueprints.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('content.blueprints.navigation_label');
    }

    public static function form(Schema $schema): Schema
    {
        return BlueprintForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BlueprintsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBlueprints::route('/'),
            'create' => CreateBlueprint::route('/create'),
            'edit' => EditBlueprint::route('/{record}/edit'),
        ];
    }
}

<?php

namespace MiPressCz\Core\Filament\Resources\Globals;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use MiPressCz\Core\Filament\Resources\Globals\Pages\CreateGlobalSet;
use MiPressCz\Core\Filament\Resources\Globals\Pages\EditGlobalSet;
use MiPressCz\Core\Filament\Resources\Globals\Pages\ListGlobalSets;
use MiPressCz\Core\Filament\Resources\Globals\Schemas\GlobalSetForm;
use MiPressCz\Core\Filament\Resources\Globals\Tables\GlobalSetsTable;
use MiPressCz\Core\Models\GlobalSet;

class GlobalSetResource extends Resource
{
    protected static ?string $model = GlobalSet::class;

    protected static string|BackedEnum|null $navigationIcon = 'far-globe';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('content.globals.navigation_group');
    }

    public static function getModelLabel(): string
    {
        return __('content.globals.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('content.globals.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('content.globals.navigation_label');
    }

    public static function form(Schema $schema): Schema
    {
        return GlobalSetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GlobalSetsTable::configure($table);
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
            'index' => ListGlobalSets::route('/'),
            'create' => CreateGlobalSet::route('/create'),
            'edit' => EditGlobalSet::route('/{record}/edit'),
        ];
    }
}

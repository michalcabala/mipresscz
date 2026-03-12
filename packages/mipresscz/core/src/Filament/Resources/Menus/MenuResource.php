<?php

namespace MiPressCz\Core\Filament\Resources\Menus;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use MiPressCz\Core\Filament\Resources\Menus\Pages\CreateMenu;
use MiPressCz\Core\Filament\Resources\Menus\Pages\EditMenu;
use MiPressCz\Core\Filament\Resources\Menus\Pages\ListMenus;
use MiPressCz\Core\Filament\Resources\Menus\Pages\ManageMenuItems;
use MiPressCz\Core\Filament\Resources\Menus\Schemas\MenuForm;
use MiPressCz\Core\Filament\Resources\Menus\Tables\MenusTable;
use MiPressCz\Core\Models\Menu;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static string|BackedEnum|null $navigationIcon = 'fal-bars';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('content.menus.navigation_group');
    }

    public static function getModelLabel(): string
    {
        return __('content.menus.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('content.menus.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('content.menus.navigation_label');
    }

    public static function form(Schema $schema): Schema
    {
        return MenuForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MenusTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenus::route('/'),
            'create' => CreateMenu::route('/create'),
            'edit' => EditMenu::route('/{record}/edit'),
            'items' => ManageMenuItems::route('/{record}/items'),
        ];
    }
}

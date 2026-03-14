<?php

namespace MiPressCz\Core\Filament\Resources\MediaFolders;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use MiPressCz\Core\Filament\Resources\MediaFolders\Pages\CreateMediaFolder;
use MiPressCz\Core\Filament\Resources\MediaFolders\Pages\EditMediaFolder;
use MiPressCz\Core\Filament\Resources\MediaFolders\Pages\TreeMediaFolders;
use MiPressCz\Core\Filament\Resources\MediaFolders\Schemas\MediaFolderForm;
use MiPressCz\Core\Models\MediaFolder;

class MediaFolderResource extends Resource
{
    protected static ?string $model = MediaFolder::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 100;

    public static function getNavigationGroup(): ?string
    {
        return __('content.entries.navigation_group');
    }

    public static function getNavigationParentItem(): ?string
    {
        return __('content.media.plural_label');
    }

    public static function getModelLabel(): string
    {
        return __('content.media_folders.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('content.media_folders.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('content.media_folders.navigation_label');
    }

    public static function form(Schema $schema): Schema
    {
        return MediaFolderForm::configure($schema);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => TreeMediaFolders::route('/'),
            'create' => CreateMediaFolder::route('/create'),
            'edit' => EditMediaFolder::route('/{record}/edit'),
        ];
    }
}

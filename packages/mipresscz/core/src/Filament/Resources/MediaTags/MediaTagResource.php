<?php

namespace MiPressCz\Core\Filament\Resources\MediaTags;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use MiPressCz\Core\Filament\Resources\MediaTags\Pages\CreateMediaTag;
use MiPressCz\Core\Filament\Resources\MediaTags\Pages\EditMediaTag;
use MiPressCz\Core\Filament\Resources\MediaTags\Pages\ListMediaTags;
use MiPressCz\Core\Filament\Resources\MediaTags\Schemas\MediaTagForm;
use MiPressCz\Core\Filament\Resources\MediaTags\Tables\MediaTagsTable;
use MiPressCz\Core\Models\MediaTag;

class MediaTagResource extends Resource
{
    protected static ?string $model = MediaTag::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 101;

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
        return __('content.media_tags.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('content.media_tags.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('content.media_tags.navigation_label');
    }

    public static function form(Schema $schema): Schema
    {
        return MediaTagForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MediaTagsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMediaTags::route('/'),
            'create' => CreateMediaTag::route('/create'),
            'edit' => EditMediaTag::route('/{record}/edit'),
        ];
    }
}

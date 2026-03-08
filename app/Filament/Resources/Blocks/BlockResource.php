<?php

namespace App\Filament\Resources\Blocks;

use App\Filament\Resources\Blocks\Pages\CreateBlock;
use App\Filament\Resources\Blocks\Pages\EditBlock;
use App\Filament\Resources\Blocks\Pages\ListBlocks;
use App\Filament\Resources\Blocks\Schemas\BlockForm;
use App\Filament\Resources\Blocks\Tables\BlocksTable;
use App\Models\Block;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class BlockResource extends Resource
{
    protected static ?string $model = Block::class;

    protected static string|BackedEnum|null $navigationIcon = 'fal-cube';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return __('content.blocks.navigation_group');
    }

    public static function getModelLabel(): string
    {
        return __('content.blocks.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('content.blocks.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('content.blocks.navigation_label');
    }

    public static function form(Schema $schema): Schema
    {
        return BlockForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BlocksTable::configure($table);
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
            'index' => ListBlocks::route('/'),
            'create' => CreateBlock::route('/create'),
            'edit' => EditBlock::route('/{record}/edit'),
        ];
    }
}

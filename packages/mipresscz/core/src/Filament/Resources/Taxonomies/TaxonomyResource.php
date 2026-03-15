<?php

namespace MiPressCz\Core\Filament\Resources\Taxonomies;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use MiPressCz\Core\Filament\Resources\Taxonomies\Pages\CreateTaxonomy;
use MiPressCz\Core\Filament\Resources\Taxonomies\Pages\EditTaxonomy;
use MiPressCz\Core\Filament\Resources\Taxonomies\Pages\ListTaxonomies;
use MiPressCz\Core\Filament\Resources\Taxonomies\Schemas\TaxonomyForm;
use MiPressCz\Core\Filament\Resources\Taxonomies\Tables\TaxonomiesTable;
use MiPressCz\Core\Models\Taxonomy;

class TaxonomyResource extends Resource
{
    protected static ?string $model = Taxonomy::class;

    protected static string|BackedEnum|null $navigationIcon = 'far-tags';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('content.taxonomies.navigation_group');
    }

    public static function getNavigationParentItem(): ?string
    {
        return __('content.collections.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('content.taxonomies.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('content.taxonomies.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('content.taxonomies.navigation_label');
    }

    public static function form(Schema $schema): Schema
    {
        return TaxonomyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaxonomiesTable::configure($table);
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
            'index' => ListTaxonomies::route('/'),
            'create' => CreateTaxonomy::route('/create'),
            'edit' => EditTaxonomy::route('/{record}/edit'),
        ];
    }
}

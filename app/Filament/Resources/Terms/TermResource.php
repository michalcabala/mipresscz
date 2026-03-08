<?php

namespace App\Filament\Resources\Terms;

use App\Filament\Resources\Terms\Pages\CreateTerm;
use App\Filament\Resources\Terms\Pages\EditTerm;
use App\Filament\Resources\Terms\Pages\ListTerms;
use App\Filament\Resources\Terms\Schemas\TermForm;
use App\Filament\Resources\Terms\Tables\TermsTable;
use App\Models\Term;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TermResource extends Resource
{
    protected static ?string $model = Term::class;

    protected static string|BackedEnum|null $navigationIcon = 'fal-tag';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('content.terms.navigation_group');
    }

    public static function getNavigationParentItem(): ?string
    {
        return __('content.taxonomies.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('content.terms.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('content.terms.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('content.terms.navigation_label');
    }

    public static function form(Schema $schema): Schema
    {
        return TermForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TermsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTerms::route('/'),
            'create' => CreateTerm::route('/create'),
            'edit' => EditTerm::route('/{record}/edit'),
        ];
    }
}

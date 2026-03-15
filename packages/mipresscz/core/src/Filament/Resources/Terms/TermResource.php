<?php

namespace MiPressCz\Core\Filament\Resources\Terms;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use MiPressCz\Core\Filament\Resources\Terms\Pages\CreateTerm;
use MiPressCz\Core\Filament\Resources\Terms\Pages\EditTerm;
use MiPressCz\Core\Filament\Resources\Terms\Pages\ListTerms;
use MiPressCz\Core\Filament\Resources\Terms\Schemas\TermForm;
use MiPressCz\Core\Filament\Resources\Terms\Tables\TermsTable;
use MiPressCz\Core\Models\Taxonomy;
use MiPressCz\Core\Models\Term;
use UnitEnum;

class TermResource extends Resource
{
    protected static ?string $model = Term::class;

    protected static string|BackedEnum|null $navigationIcon = 'far-tag';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 1;

    protected static ?string $configurationClass = TermResourceConfiguration::class;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        if (static::hasConfiguration()) {
            return __('content.entries.navigation_group');
        }

        return __('content.terms.navigation_group');
    }

    public static function getNavigationParentItem(): ?string
    {
        return static::getConfiguration()?->getNavigationParentItem();
    }

    public static function getModelLabel(): string
    {
        return __('content.terms.label');
    }

    public static function getPluralModelLabel(): string
    {
        if ($configuration = static::getConfiguration()) {
            if ($label = $configuration->getNavigationLabel()) {
                return $label;
            }
        }

        return __('content.terms.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        if ($configuration = static::getConfiguration()) {
            if ($label = $configuration->getNavigationLabel()) {
                return $label;
            }
        }

        return __('content.terms.navigation_label');
    }

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        if ($configuration = static::getConfiguration()) {
            if ($icon = $configuration->getNavigationIcon()) {
                return $icon;
            }
        }

        return static::$navigationIcon;
    }

    public static function getNavigationSort(): ?int
    {
        if ($configuration = static::getConfiguration()) {
            if ($sort = $configuration->getNavigationSort()) {
                return $sort;
            }
        }

        return static::$navigationSort;
    }

    public static function getTaxonomyHandle(): ?string
    {
        return static::getConfiguration()?->getTaxonomyHandle();
    }

    public static function getScopedTaxonomy(): ?Taxonomy
    {
        $taxonomyHandle = static::getTaxonomyHandle();

        if (blank($taxonomyHandle)) {
            return null;
        }

        return Taxonomy::query()
            ->where('handle', $taxonomyHandle)
            ->first();
    }

    public static function form(Schema $schema): Schema
    {
        return TermForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TermsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['taxonomy', 'parent']);

        if ($handle = static::getTaxonomyHandle()) {
            $query->whereHas('taxonomy', fn (Builder $taxonomyQuery) => $taxonomyQuery->where('handle', $handle));
        }

        return $query;
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

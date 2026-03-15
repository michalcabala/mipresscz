<?php

namespace MiPressCz\Core\Filament\Resources\Terms\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use MiPressCz\Core\Filament\Resources\Terms\TermResource;

class ListTerms extends ListRecords
{
    protected static string $resource = TermResource::class;

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function mount(): void
    {
        parent::mount();

        if (TermResource::getTaxonomyHandle()) {
            return;
        }

        if ($taxonomyId = request()->query('taxonomy_id')) {
            $this->tableFilters['taxonomy_id'] = ['value' => $taxonomyId];
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\Terms\Pages;

use App\Filament\Resources\Terms\TermResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTerms extends ListRecords
{
    protected static string $resource = TermResource::class;

    public function mount(): void
    {
        parent::mount();

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

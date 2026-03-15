<?php

namespace MiPressCz\Core\Filament\Resources\Taxonomies\Pages;

use Filament\Resources\Pages\CreateRecord;
use MiPressCz\Core\Filament\Resources\Taxonomies\TaxonomyResource;

class CreateTaxonomy extends CreateRecord
{
    protected static string $resource = TaxonomyResource::class;

    protected ?string $selectedCollectionId = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->selectedCollectionId = $data['collection_id'] ?? null;
        unset($data['collection_id']);
        $data['name'] = $data['handle'];

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->collections()->sync(array_filter([$this->selectedCollectionId]));
    }
}

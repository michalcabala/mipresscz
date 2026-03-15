<?php

namespace MiPressCz\Core\Filament\Resources\Taxonomies\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use MiPressCz\Core\Filament\Resources\Taxonomies\TaxonomyResource;

class EditTaxonomy extends EditRecord
{
    protected static string $resource = TaxonomyResource::class;

    protected ?string $selectedCollectionId = null;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->selectedCollectionId = $data['collection_id'] ?? null;
        unset($data['collection_id']);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->collections()->sync(array_filter([$this->selectedCollectionId]));
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

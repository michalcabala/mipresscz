<?php

namespace MiPressCz\Core\Filament\Resources\Taxonomies\Pages;

use Filament\Resources\Pages\CreateRecord;
use MiPressCz\Core\Filament\Resources\Taxonomies\TaxonomyResource;

class CreateTaxonomy extends CreateRecord
{
    protected static string $resource = TaxonomyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['name'] = $data['handle'];

        return $data;
    }
}

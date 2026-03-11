<?php

namespace MiPressCz\Core\Filament\Resources\Collections\Pages;

use Filament\Resources\Pages\CreateRecord;
use MiPressCz\Core\Filament\Resources\Collections\CollectionResource;

class CreateCollection extends CreateRecord
{
    protected static string $resource = CollectionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['name'] = $data['handle'];

        return $data;
    }
}

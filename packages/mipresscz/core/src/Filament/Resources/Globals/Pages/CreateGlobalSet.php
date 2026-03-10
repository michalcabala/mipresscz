<?php

namespace MiPressCz\Core\Filament\Resources\Globals\Pages;

use Filament\Resources\Pages\CreateRecord;
use MiPressCz\Core\Filament\Resources\Globals\GlobalSetResource;

class CreateGlobalSet extends CreateRecord
{
    protected static string $resource = GlobalSetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['name'] = $data['handle'];

        return $data;
    }
}

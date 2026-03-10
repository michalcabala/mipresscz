<?php

namespace MiPressCz\Core\Filament\Resources\Blueprints\Pages;

use Filament\Resources\Pages\CreateRecord;
use MiPressCz\Core\Filament\Resources\Blueprints\BlueprintResource;

class CreateBlueprint extends CreateRecord
{
    protected static string $resource = BlueprintResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['name'] = $data['handle'];

        return $data;
    }
}

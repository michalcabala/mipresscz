<?php

namespace MiPressCz\Core\Filament\Resources\Blueprints\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use MiPressCz\Core\Filament\Resources\Blueprints\BlueprintResource;

class ListBlueprints extends ListRecords
{
    protected static string $resource = BlueprintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

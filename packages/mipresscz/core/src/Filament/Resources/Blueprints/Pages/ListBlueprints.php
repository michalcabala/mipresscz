<?php

namespace MiPressCz\Core\Filament\Resources\Blueprints\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use MiPressCz\Core\Filament\Resources\Blueprints\BlueprintResource;

class ListBlueprints extends ListRecords
{
    protected static string $resource = BlueprintResource::class;

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

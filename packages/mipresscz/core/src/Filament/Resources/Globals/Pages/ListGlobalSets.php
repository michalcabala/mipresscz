<?php

namespace MiPressCz\Core\Filament\Resources\Globals\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use MiPressCz\Core\Filament\Resources\Globals\GlobalSetResource;

class ListGlobalSets extends ListRecords
{
    protected static string $resource = GlobalSetResource::class;

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

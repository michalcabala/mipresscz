<?php

namespace App\Filament\Resources\Globals\Pages;

use App\Filament\Resources\Globals\GlobalSetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGlobalSets extends ListRecords
{
    protected static string $resource = GlobalSetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

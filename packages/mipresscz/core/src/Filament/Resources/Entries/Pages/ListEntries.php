<?php

namespace MiPressCz\Core\Filament\Resources\Entries\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use MiPressCz\Core\Filament\Resources\Entries\EntryResource;

class ListEntries extends ListRecords
{
    protected static string $resource = EntryResource::class;

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

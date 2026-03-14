<?php

namespace MiPressCz\Core\Filament\Resources\MediaTags\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use MiPressCz\Core\Filament\Resources\MediaTags\MediaTagResource;

class ListMediaTags extends ListRecords
{
    protected static string $resource = MediaTagResource::class;

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

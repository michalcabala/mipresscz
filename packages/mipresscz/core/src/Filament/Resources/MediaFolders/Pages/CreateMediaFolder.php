<?php

namespace MiPressCz\Core\Filament\Resources\MediaFolders\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use MiPressCz\Core\Filament\Resources\MediaFolders\MediaFolderResource;

class CreateMediaFolder extends CreateRecord
{
    protected static string $resource = MediaFolderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = Str::slug($data['name']);

        return $data;
    }
}

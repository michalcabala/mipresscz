<?php

namespace MiPressCz\Core\Filament\Resources\MediaTags\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use MiPressCz\Core\Filament\Resources\MediaTags\MediaTagResource;

class CreateMediaTag extends CreateRecord
{
    protected static string $resource = MediaTagResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = Str::slug($data['name']);

        return $data;
    }
}

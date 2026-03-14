<?php

namespace MiPressCz\Core\Filament\Resources\MediaTags\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;
use MiPressCz\Core\Filament\Resources\MediaTags\MediaTagResource;

class EditMediaTag extends EditRecord
{
    protected static string $resource = MediaTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['slug'] = Str::slug($data['name']);

        return $data;
    }
}

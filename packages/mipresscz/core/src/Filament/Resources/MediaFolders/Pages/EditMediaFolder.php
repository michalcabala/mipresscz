<?php

namespace MiPressCz\Core\Filament\Resources\MediaFolders\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;
use MiPressCz\Core\Filament\Resources\MediaFolders\MediaFolderResource;

class EditMediaFolder extends EditRecord
{
    protected static string $resource = MediaFolderResource::class;

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

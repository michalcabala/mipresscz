<?php

namespace MiPressCz\Core\Filament\Resources\Entries\Pages;

use Filament\Resources\Pages\CreateRecord;
use MiPressCz\Core\Filament\Resources\Entries\EntryResource;
use MiPressCz\Core\Models\Collection;

class CreateEntry extends CreateRecord
{
    protected static string $resource = EntryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['author_id'] ??= auth()->id();

        if ($handle = EntryResource::getCollectionHandle()) {
            $data['collection_id'] = Collection::query()
                ->where('handle', $handle)
                ->value('id');
        }

        return $data;
    }
}

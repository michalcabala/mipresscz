<?php

namespace App\Filament\Resources\Entries\Pages;

use App\Filament\Resources\Entries\EntryResource;
use App\Models\Collection;
use Filament\Resources\Pages\CreateRecord;

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

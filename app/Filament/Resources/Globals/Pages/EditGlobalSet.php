<?php

namespace App\Filament\Resources\Globals\Pages;

use App\Filament\Resources\Globals\GlobalSetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGlobalSet extends EditRecord
{
    protected static string $resource = GlobalSetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

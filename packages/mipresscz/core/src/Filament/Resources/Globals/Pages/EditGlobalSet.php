<?php

namespace MiPressCz\Core\Filament\Resources\Globals\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use MiPressCz\Core\Filament\Resources\Globals\GlobalSetResource;

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

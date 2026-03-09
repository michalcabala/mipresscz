<?php

namespace MiPressCz\Core\Filament\Resources\Blueprints\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use MiPressCz\Core\Filament\Resources\Blueprints\BlueprintResource;

class EditBlueprint extends EditRecord
{
    protected static string $resource = BlueprintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

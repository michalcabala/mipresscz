<?php

namespace MiPressCz\Core\Filament\Resources\Menus\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use MiPressCz\Core\Filament\Resources\Menus\MenuResource;
use MiPressCz\Core\Models\Menu;

class EditMenu extends EditRecord
{
    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('manage_items')
                ->label(__('content.menus.manage_items'))
                ->icon('fal-list-tree')
                ->color('gray')
                ->url(fn (Menu $record): string => MenuResource::getUrl('items', ['record' => $record])),

            DeleteAction::make(),
        ];
    }
}

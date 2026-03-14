<?php

namespace MiPressCz\Core\Filament\Resources\Entries\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;
use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Filament\Resources\Entries\EntryResource;
use MiPressCz\Core\Models\Collection;

class CreateEntry extends CreateRecord
{
    protected static string $resource = EntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save_as_draft')
                ->label(__('content.actions.save_draft'))
                ->color('gray')
                ->action(fn () => $this->create())
                ->keyBindings(['mod+s']),
            Action::make('publish')
                ->label(__('content.actions.publish'))
                ->icon(Heroicon::OutlinedCheckCircle)
                ->visible(fn (): bool => auth()->user()->hasPermissionTo('publish.entries'))
                ->action(function () {
                    $this->form->getState();
                    $this->data['status'] = EntryStatus::Published->value;
                    $this->data['published_at'] ??= now()->toDateTimeString();
                    $this->create();
                }),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['author_id'] ??= auth()->id();
        $data['locale'] ??= locales()->getDefaultCode();

        if ($handle = EntryResource::getCollectionHandle()) {
            $collection = Collection::query()
                ->where('handle', $handle)
                ->with('blueprints')
                ->first();

            if ($collection) {
                $data['collection_id'] = $collection->id;
                $data['blueprint_id'] ??= $collection->defaultBlueprint()?->id
                    ?? $collection->blueprints->first()?->id;
            }
        }

        return $data;
    }
}

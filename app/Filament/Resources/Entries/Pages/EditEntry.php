<?php

namespace App\Filament\Resources\Entries\Pages;

use App\Filament\Resources\Entries\EntryResource;
use App\Models\Entry;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;

class EditEntry extends EditRecord
{
    protected static string $resource = EntryResource::class;

    protected function resolveRecord(int|string $key): Entry
    {
        return Entry::with(['translations', 'origin.translations'])
            ->findOrFail($key);
    }

    protected function getHeaderActions(): array
    {
        return [
            ...$this->getLocaleActions(),
            DeleteAction::make(),
        ];
    }

    /** @return list<Action> */
    protected function getLocaleActions(): array
    {
        /** @var Entry $record */
        $record = $this->getRecord();
        $translations = $record->getTranslations();

        if ($translations->count() <= 1) {
            return [];
        }

        $flagMap = [
            'cs' => 'CZ.svg',
            'en' => 'GB-UKM.svg',
        ];

        return $translations
            ->map(function (Entry $entry, string $locale) use ($record, $flagMap): Action {
                $isCurrent = $record->id === $entry->id;
                $flagFile = $flagMap[$locale] ?? null;

                $flagHtml = $flagFile
                    ? '<img src="'.e(asset("assets/flags/{$flagFile}")).'" alt="'.e($locale).'" style="display:inline-block;width:1.25rem;height:.9rem;border-radius:2px;vertical-align:middle;margin-right:.3rem;" />'
                    : '';

                return Action::make("locale_{$locale}")
                    ->label(new HtmlString($flagHtml.strtoupper($locale)))
                    ->url($isCurrent ? null : static::getResource()::getUrl('edit', ['record' => $entry->id]))
                    ->color($isCurrent ? 'primary' : 'gray')
                    ->outlined(! $isCurrent)
                    ->disabled($isCurrent)
                    ->size('sm');
            })
            ->values()
            ->all();
    }
}

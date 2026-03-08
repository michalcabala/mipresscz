<?php

namespace App\Filament\Resources\Entries\Pages;

use App\Filament\Resources\Entries\EntryResource;
use App\Models\Entry;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

class EditEntry extends EditRecord
{
    protected static string $resource = EntryResource::class;

    protected function resolveRecord(int|string $key): Entry
    {
        return Entry::with(['translations', 'origin.translations', 'blueprint'])
            ->findOrFail($key);
    }

    protected function getHeaderActions(): array
    {
        return [
            ...$this->getLocaleActions(),
            DeleteAction::make(),
        ];
    }

    /** @return list<Action|ActionGroup> */
    protected function getLocaleActions(): array
    {
        /** @var Entry $record */
        $record = $this->getRecord();
        $translations = $record->getTranslations();
        $missingLocales = $record->getMissingLocales();

        $flagMap = [
            'cs' => 'CZ.svg',
            'en' => 'GB-UKM.svg',
        ];

        $currentLocale = $record->locale;
        $currentFlagFile = $flagMap[$currentLocale] ?? null;
        $currentFlagHtml = $currentFlagFile
            ? '<img src="'.e(asset("assets/flags/{$currentFlagFile}")).'" alt="'.e($currentLocale).'" style="display:inline-block;width:1.25rem;height:.9rem;border-radius:2px;vertical-align:middle;margin-right:.3rem;" />'
            : '';

        // Existing translations (excluding current)
        $switchItems = $translations
            ->reject(fn (Entry $entry) => $entry->id === $record->id)
            ->map(function (Entry $entry, string $locale) use ($flagMap): Action {
                $flagFile = $flagMap[$locale] ?? null;
                $flagHtml = $flagFile
                    ? '<img src="'.e(asset("assets/flags/{$flagFile}")).'" alt="'.e($locale).'" style="display:inline-block;width:1.25rem;height:.9rem;border-radius:2px;vertical-align:middle;margin-right:.3rem;" />'
                    : '';

                return Action::make("locale_switch_{$locale}")
                    ->label(new HtmlString($flagHtml.strtoupper($locale)))
                    ->url(static::getResource()::getUrl('edit', ['record' => $entry->id]))
                    ->color('gray');
            })
            ->values()
            ->all();

        // Create translation actions for missing locales
        $createItems = collect($missingLocales)
            ->map(function (string $locale) use ($record, $flagMap): Action {
                $flagFile = $flagMap[$locale] ?? null;
                $flagHtml = $flagFile
                    ? '<img src="'.e(asset("assets/flags/{$flagFile}")).'" alt="'.e($locale).'" style="display:inline-block;width:1.25rem;height:.9rem;border-radius:2px;vertical-align:middle;margin-right:.3rem;" />'
                    : '';

                return Action::make("locale_create_{$locale}")
                    ->label(new HtmlString($flagHtml.strtoupper($locale).' +'))
                    ->icon(Heroicon::Plus)
                    ->color('success')
                    ->action(function () use ($record, $locale) {
                        $origin = $record->getOrigin();

                        $translation = Entry::create([
                            'collection_id' => $origin->collection_id,
                            'blueprint_id' => $origin->blueprint_id,
                            'origin_id' => $origin->id,
                            'locale' => $locale,
                            'title' => $origin->title,
                            'slug' => $origin->slug.'-'.$locale,
                            'status' => $origin->status,
                            'author_id' => auth()->id() ?? $origin->author_id,
                            'parent_id' => $origin->parent_id,
                            'order' => $origin->order,
                            'data' => $origin->getNonTranslatableData(),
                            'content' => null,
                            'featured_image_id' => $origin->featured_image_id,
                        ]);

                        $this->redirect(static::getResource()::getUrl('edit', ['record' => $translation->id]));
                    });
            })
            ->all();

        $allItems = array_merge($switchItems, $createItems);

        if (empty($allItems)) {
            return [];
        }

        return [
            ActionGroup::make($allItems)
                ->label(new HtmlString($currentFlagHtml.strtoupper($currentLocale)))
                ->color('gray')
                ->button()
                ->dropdownPlacement('bottom-end'),
        ];
    }
}

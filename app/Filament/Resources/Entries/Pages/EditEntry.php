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

    protected function renderFlagHtml(?string $flagFile, string $alt): string
    {
        if (! $flagFile) {
            return '';
        }

        $url = e(asset("assets/flags/{$flagFile}"));

        return '<span class="inline-flex items-center justify-center w-5 h-5 rounded-full overflow-hidden align-middle mr-1 shrink-0"><img src="'.$url.'" alt="'.e($alt).'" class="w-full h-full object-cover" /></span>';
    }

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

        /** @var array<string, string> $flagMap code => filename */
        $flagMap = locales()->getActive()
            ->filter(fn ($l) => $l->flag !== null)
            ->mapWithKeys(fn ($l) => [$l->code => $l->flag])
            ->all();

        $currentLocale = $record->locale;
        $currentFlagFile = $flagMap[$currentLocale] ?? null;
        $currentFlagHtml = $this->renderFlagHtml($currentFlagFile, $currentLocale);

        // Existing translations (excluding current)
        $switchItems = $translations
            ->reject(fn (Entry $entry) => $entry->id === $record->id)
            ->map(function (Entry $entry, string $locale) use ($flagMap): Action {
                $flagHtml = $this->renderFlagHtml($flagMap[$locale] ?? null, $locale);

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
                $flagHtml = $this->renderFlagHtml($flagMap[$locale] ?? null, $locale);

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

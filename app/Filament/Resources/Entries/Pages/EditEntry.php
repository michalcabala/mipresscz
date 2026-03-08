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

    protected function renderFlagHtml(?string $flagFile, string $alt, string $size = 'w-5 h-5'): string
    {
        if (! $flagFile) {
            return '<span class="inline-flex items-center justify-center '.$size.' rounded-full bg-gray-100 dark:bg-gray-700 align-middle mr-1.5 shrink-0 text-[10px] font-bold text-gray-500">'.e(strtoupper(mb_substr($alt, 0, 2))).'</span>';
        }

        $url = e(asset("assets/flags/{$flagFile}"));

        return '<span class="inline-flex items-center justify-center '.$size.' rounded-full overflow-hidden align-middle mr-1.5 shrink-0"><img src="'.$url.'" alt="'.e($alt).'" class="w-full h-full object-cover" /></span>';
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

        $activeLocales = locales()->getActive();
        $localeMap = $activeLocales->keyBy('code');

        $currentLocale = $record->locale;
        $currentLocaleModel = $localeMap->get($currentLocale);
        $currentFlagHtml = $this->renderFlagHtml($currentLocaleModel?->flag, $currentLocale, 'w-6 h-6');

        $existingCount = $translations->count();
        $totalCount = $existingCount + count($missingLocales);

        // Existing translations (excluding current)
        $switchItems = $translations
            ->reject(fn (Entry $entry) => $entry->id === $record->id)
            ->map(function (Entry $entry, string $locale) use ($localeMap): Action {
                $localeModel = $localeMap->get($locale);
                $flagHtml = $this->renderFlagHtml($localeModel?->flag, $locale);
                $label = $localeModel?->native_name ?? strtoupper($locale);

                return Action::make("locale_switch_{$locale}")
                    ->label(new HtmlString($flagHtml.e($label)))
                    ->url(static::getResource()::getUrl('edit', ['record' => $entry->id]))
                    ->color('gray');
            })
            ->values()
            ->all();

        // Create translation actions for missing locales
        $createItems = collect($missingLocales)
            ->map(function (string $locale) use ($record, $localeMap): Action {
                $localeModel = $localeMap->get($locale);
                $flagHtml = $this->renderFlagHtml($localeModel?->flag, $locale);
                $label = $localeModel?->native_name ?? strtoupper($locale);

                return Action::make("locale_create_{$locale}")
                    ->label(new HtmlString($flagHtml.e($label)))
                    ->icon(Heroicon::Plus)
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(__('content.actions.create_translation_for', ['locale' => $label]))
                    ->modalDescription(__('content.actions.create_translation_confirm', ['locale' => $label]))
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

        if (empty($switchItems) && empty($createItems)) {
            return [];
        }

        // Build dropdown items with a divider between groups
        $dropdownItems = [];

        if (! empty($switchItems)) {
            $dropdownItems[] = ActionGroup::make($switchItems)->dropdown(false);
        }

        if (! empty($createItems)) {
            $dropdownItems[] = ActionGroup::make($createItems)->dropdown(false);
        }

        $triggerLabel = $currentFlagHtml.e(strtoupper($currentLocale));
        if ($totalCount > 1) {
            $triggerLabel .= ' <span class="text-xs text-gray-400 ml-1">'.$existingCount.'/'.$totalCount.'</span>';
        }

        return [
            ActionGroup::make($dropdownItems)
                ->label(new HtmlString($triggerLabel))
                ->color('gray')
                ->button()
                ->dropdownPlacement('bottom-end'),
        ];
    }
}

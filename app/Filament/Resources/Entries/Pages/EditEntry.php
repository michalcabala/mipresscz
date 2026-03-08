<?php

namespace App\Filament\Resources\Entries\Pages;

use App\Filament\Resources\Entries\EntryResource;
use App\Models\Entry;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;

class EditEntry extends EditRecord
{
    protected static string $resource = EntryResource::class;

    protected function renderFlagHtml(?string $flagFile, string $alt, string $size = 'size-5'): string
    {
        if (! $flagFile) {
            return '<span class="inline-flex items-center justify-center '.$size.' rounded-full bg-gray-100 dark:bg-gray-700 shrink-0 text-[10px] font-bold text-gray-500 uppercase">'.e(mb_substr($alt, 0, 2)).'</span>';
        }

        $url = e(asset("assets/flags/{$flagFile}"));

        return '<span class="inline-flex items-center justify-center '.$size.' rounded-full overflow-hidden shrink-0"><img src="'.$url.'" alt="'.e($alt).'" class="w-full h-full object-cover" /></span>';
    }

    protected function buildLabel(string $flagHtml, string $text, ?string $suffix = null): HtmlString
    {
        $html = '<span class="flex items-center gap-x-2">'.$flagHtml.'<span class="truncate">'.e($text).'</span>';

        if ($suffix) {
            $html .= $suffix;
        }

        $html .= '</span>';

        return new HtmlString($html);
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

        $localeMap = locales()->getActive()->keyBy('code');

        $currentLocale = $record->locale;
        $currentLocaleModel = $localeMap->get($currentLocale);
        $currentFlagHtml = $this->renderFlagHtml($currentLocaleModel?->flag, $currentLocale, 'size-6');

        $existingCount = $translations->count();
        $totalCount = $existingCount + count($missingLocales);

        // Existing translations (excluding current)
        $switchItems = $translations
            ->reject(fn (Entry $entry) => $entry->id === $record->id)
            ->map(function (Entry $entry, string $locale) use ($localeMap): Action {
                $localeModel = $localeMap->get($locale);
                $flagHtml = $this->renderFlagHtml($localeModel?->flag, $locale);
                $name = $localeModel?->native_name ?? strtoupper($locale);

                return Action::make("locale_switch_{$locale}")
                    ->label($this->buildLabel($flagHtml, $name))
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
                $name = $localeModel?->native_name ?? strtoupper($locale);

                return Action::make("locale_create_{$locale}")
                    ->label($this->buildLabel($flagHtml, $name))
                    ->badge('+')
                    ->badgeColor('success')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading(__('content.actions.create_translation_for', ['locale' => $name]))
                    ->modalDescription(__('content.actions.create_translation_confirm', ['locale' => $name]))
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

        // Trigger button label
        $counterHtml = ($totalCount > 1)
            ? '<span class="text-xs opacity-50 tabular-nums">'.$existingCount.'/'.$totalCount.'</span>'
            : '';

        $triggerLabel = $this->buildLabel($currentFlagHtml, strtoupper($currentLocale), $counterHtml);

        return [
            ActionGroup::make($dropdownItems)
                ->label($triggerLabel)
                ->color('gray')
                ->button()
                ->dropdownPlacement('bottom-end'),
        ];
    }
}

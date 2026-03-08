<?php

namespace App\Filament\Resources\Entries\Pages;

use App\Filament\Resources\Entries\EntryResource;
use App\Models\Entry;
use App\Models\Locale;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Collection;
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

    /**
     * Inline round flag <img> tag that works inside Filament's truncated
     * dropdown-item labels (no wrapper spans, just a plain <img>).
     */
    protected function flagImg(?string $flagFile, string $alt, string $sizeClass = 'size-5'): string
    {
        if (! $flagFile) {
            return '';
        }

        $url = e(asset("assets/flags/{$flagFile}"));

        return '<img src="'.$url.'" alt="'.e($alt).'" class="inline '.$sizeClass.' rounded-full object-cover align-middle" />';
    }

    /** @return list<Action|ActionGroup> */
    protected function getLocaleActions(): array
    {
        /** @var Entry $record */
        $record = $this->getRecord();
        $translations = $record->getTranslations();
        $missingLocales = $record->getMissingLocales();

        /** @var Collection<string, Locale> $localeMap */
        $localeMap = locales()->getActive()->keyBy('code');

        if ($localeMap->count() <= 1) {
            return [];
        }

        $currentLocale = $record->locale;
        $currentModel = $localeMap->get($currentLocale);

        $existingCount = $translations->count();
        $totalCount = $existingCount + count($missingLocales);

        // --- Switch to existing translation ---
        $switchItems = $translations
            ->reject(fn (Entry $entry) => $entry->id === $record->id)
            ->map(function (Entry $entry, string $locale) use ($localeMap): Action {
                $model = $localeMap->get($locale);
                $flag = $this->flagImg($model?->flag, $locale, 'size-4');
                $name = $model?->native_name ?? strtoupper($locale);

                return Action::make("locale_switch_{$locale}")
                    ->label(new HtmlString($flag ? "{$flag} ".e($name) : e($name)))
                    ->badge(strtoupper($locale))
                    ->badgeColor('gray')
                    ->color('gray')
                    ->url(static::getResource()::getUrl('edit', ['record' => $entry->id]));
            })
            ->values()
            ->all();

        // --- Create missing translation ---
        $createItems = collect($missingLocales)
            ->map(function (string $locale) use ($record, $localeMap): Action {
                $model = $localeMap->get($locale);
                $flag = $this->flagImg($model?->flag, $locale, 'size-4');
                $name = $model?->native_name ?? strtoupper($locale);

                return Action::make("locale_create_{$locale}")
                    ->label(new HtmlString($flag ? "{$flag} ".e($name) : e($name)))
                    ->badge(__('content.actions.add_short'))
                    ->badgeColor('success')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading(__('content.actions.create_translation_for', ['locale' => $name]))
                    ->modalDescription(__('content.actions.create_translation_confirm', ['locale' => $name]))
                    ->action(function () use ($record, $locale): void {
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

        // --- Group with divider between existing / missing ---
        $dropdownItems = [];

        if (! empty($switchItems)) {
            $dropdownItems[] = ActionGroup::make($switchItems)->dropdown(false);
        }

        if (! empty($createItems)) {
            $dropdownItems[] = ActionGroup::make($createItems)->dropdown(false);
        }

        // --- Trigger button ---
        $triggerFlag = $this->flagImg($currentModel?->flag, $currentLocale, 'size-5');
        $triggerText = strtoupper($currentLocale);
        $triggerHtml = $triggerFlag
            ? "{$triggerFlag} {$triggerText}"
            : $triggerText;

        return [
            ActionGroup::make($dropdownItems)
                ->label(new HtmlString($triggerHtml))
                ->badge($totalCount > 1 ? "{$existingCount}/{$totalCount}" : null)
                ->badgeColor($existingCount < $totalCount ? 'warning' : 'success')
                ->color('gray')
                ->button()
                ->dropdownPlacement('bottom-end'),
        ];
    }
}

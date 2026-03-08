<?php

namespace App\View\Components;

use App\Models\Entry;
use App\Models\Locale;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class LanguageSwitcher extends Component
{
    /** @var Collection<int, array{locale: Locale, url: string, is_current: bool}> */
    public Collection $items;

    public function __construct(public ?Entry $entry = null)
    {
        $currentLocale = app()->getLocale();

        $this->items = locales()->getFrontendLocales()->map(function (Locale $locale) use ($currentLocale): array {
            $url = null;

            if ($this->entry) {
                $translation = $this->entry->getTranslation($locale->code);
                $url = $translation?->getFullUrl();
            }

            if ($url === null) {
                $url = $locale->url_prefix ? url($locale->url_prefix) : url('/');
            }

            return [
                'locale' => $locale,
                'url' => $url,
                'is_current' => $locale->code === $currentLocale,
            ];
        });
    }

    public function render(): View|Closure|string
    {
        return view('components.language-switcher');
    }
}

<?php

namespace App\Observers;

use App\Models\Locale;

class LocaleObserver
{
    public function created(Locale $locale): void
    {
        locales()->clearCache();
    }

    public function updated(Locale $locale): void
    {
        locales()->clearCache();

        // Ensure only one default locale exists
        if ($locale->is_default && $locale->wasChanged('is_default')) {
            Locale::query()
                ->where('id', '!=', $locale->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    }

    public function deleted(Locale $locale): void
    {
        locales()->clearCache();
    }

    public function restored(Locale $locale): void
    {
        locales()->clearCache();
    }

    public function forceDeleted(Locale $locale): void
    {
        locales()->clearCache();
    }
}

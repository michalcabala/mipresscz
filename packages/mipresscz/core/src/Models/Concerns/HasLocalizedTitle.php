<?php

namespace MiPressCz\Core\Models\Concerns;

trait HasLocalizedTitle
{
    public function getLocalizedTitle(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $translations = $this->translations ?? [];

        return $translations[$locale]['title'] ?? $this->title;
    }

    public function getLocalizedDescription(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        $translations = $this->translations ?? [];

        return $translations[$locale]['description'] ?? $this->description ?? null;
    }
}

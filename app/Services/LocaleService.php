<?php

namespace App\Services;

use App\Models\Locale;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class LocaleService
{
    public const CACHE_KEY = 'mipress.locales';

    public function getAll(): Collection
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return Locale::query()->orderBy('order')->get();
        });
    }

    public function getActive(): Collection
    {
        return $this->getAll()->where('is_active', true)->values();
    }

    public function getFrontendLocales(): Collection
    {
        return $this->getAll()
            ->where('is_active', true)
            ->where('is_frontend_available', true)
            ->values();
    }

    public function getAdminLocales(): Collection
    {
        return $this->getAll()
            ->where('is_active', true)
            ->where('is_admin_available', true)
            ->values();
    }

    public function getDefault(): ?Locale
    {
        return $this->getAll()->firstWhere('is_default', true);
    }

    public function getDefaultCode(): string
    {
        return $this->getDefault()?->code ?? 'cs';
    }

    public function findByCode(string $code): ?Locale
    {
        return $this->getAll()->firstWhere('code', $code);
    }

    /** @return list<string> */
    public function getActiveCodes(): array
    {
        return $this->getActive()->pluck('code')->all();
    }

    /** @return array<string, string> */
    public function toSelectOptions(): array
    {
        return $this->getActive()
            ->mapWithKeys(fn (Locale $locale) => [$locale->code => $locale->native_name])
            ->all();
    }

    /**
     * Returns data for LanguageSwitch plugin.
     *
     * @return array{locales: list<string>, labels: array<string, string>, flags: array<string, string>}
     */
    public function toLanguageSwitchConfig(): array
    {
        $locales = $this->getAdminLocales();

        return [
            'locales' => $locales->pluck('code')->all(),
            'labels' => $locales->mapWithKeys(fn (Locale $l) => [$l->code => $l->native_name])->all(),
            'flags' => $locales
                ->filter(fn (Locale $l) => $l->flag !== null)
                ->mapWithKeys(fn (Locale $l) => [$l->code => asset("assets/flags/{$l->flag}")])
                ->all(),
        ];
    }

    public function isMultilingual(): bool
    {
        return $this->getActive()->count() > 1;
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}

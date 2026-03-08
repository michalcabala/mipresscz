<?php

namespace App\Facades;

use App\Services\LocaleService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Support\Collection getAll()
 * @method static \Illuminate\Support\Collection getActive()
 * @method static \Illuminate\Support\Collection getFrontendLocales()
 * @method static \Illuminate\Support\Collection getAdminLocales()
 * @method static \App\Models\Locale|null getDefault()
 * @method static string getDefaultCode()
 * @method static \App\Models\Locale|null findByCode(string $code)
 * @method static list<string> getActiveCodes()
 * @method static array<string, string> toSelectOptions()
 * @method static array toLanguageSwitchConfig()
 * @method static bool isMultilingual()
 * @method static void clearCache()
 *
 * @see \App\Services\LocaleService
 */
class Locales extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return LocaleService::class;
    }
}

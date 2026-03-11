<?php

namespace MiPressCz\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use MiPressCz\Core\Models\Locale;

class LocaleSeeder extends Seeder
{
    public function run(): void
    {
        $locales = [
            [
                'code' => 'cs',
                'name' => 'Czech',
                'native_name' => 'Čeština',
                'flag' => 'CZ.svg',
                'is_default' => true,
                'is_active' => true,
                'is_admin_available' => true,
                'is_frontend_available' => true,
                'direction' => 'ltr',
                'date_format' => 'd.m.Y',
                'url_prefix' => null,
                'fallback_locale' => null,
                'order' => 1,
            ],
            [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'flag' => 'GB-UKM.svg',
                'is_default' => false,
                'is_active' => true,
                'is_admin_available' => true,
                'is_frontend_available' => true,
                'direction' => 'ltr',
                'date_format' => 'd/m/Y',
                'url_prefix' => 'en',
                'fallback_locale' => 'cs',
                'order' => 2,
            ],
        ];

        foreach ($locales as $data) {
            Locale::updateOrCreate(['code' => $data['code']], $data);
        }
    }
}

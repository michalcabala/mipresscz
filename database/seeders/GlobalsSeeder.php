<?php

namespace Database\Seeders;

use App\Models\GlobalSet;
use Illuminate\Database\Seeder;

class GlobalsSeeder extends Seeder
{
    public function run(): void
    {
        $globals = [
            [
                'name' => 'site',
                'title' => 'Nastavení webu',
                'handle' => 'site',
                'fields' => [
                    ['handle' => 'site_name', 'type' => 'text', 'display' => 'Název webu', 'required' => true, 'translatable' => true, 'width' => 50, 'section' => 'main', 'config' => [], 'order' => 1],
                    ['handle' => 'site_description', 'type' => 'textarea', 'display' => 'Popis webu', 'required' => false, 'translatable' => true, 'width' => 50, 'section' => 'main', 'config' => [], 'order' => 2],
                    ['handle' => 'logo', 'type' => 'media', 'display' => 'Logo', 'required' => false, 'translatable' => false, 'width' => 50, 'section' => 'main', 'config' => ['max_files' => 1], 'order' => 3],
                    ['handle' => 'favicon', 'type' => 'media', 'display' => 'Favicon', 'required' => false, 'translatable' => false, 'width' => 50, 'section' => 'main', 'config' => ['max_files' => 1], 'order' => 4],
                    ['handle' => 'contact_email', 'type' => 'email', 'display' => 'Kontaktní e-mail', 'required' => false, 'translatable' => false, 'width' => 50, 'section' => 'main', 'config' => [], 'order' => 5],
                    ['handle' => 'phone', 'type' => 'phone', 'display' => 'Telefon', 'required' => false, 'translatable' => false, 'width' => 50, 'section' => 'main', 'config' => [], 'order' => 6],
                ],
                'data' => [
                    'site_name' => 'miPress',
                    'site_description' => 'Moderní CMS postavený na Laravel a Filament',
                    'contact_email' => 'info@mipress.cz',
                ],
            ],
            [
                'name' => 'social',
                'title' => 'Sociální sítě',
                'handle' => 'social',
                'fields' => [
                    ['handle' => 'facebook', 'type' => 'url', 'display' => 'Facebook', 'required' => false, 'translatable' => false, 'width' => 50, 'section' => 'main', 'config' => [], 'order' => 1],
                    ['handle' => 'instagram', 'type' => 'url', 'display' => 'Instagram', 'required' => false, 'translatable' => false, 'width' => 50, 'section' => 'main', 'config' => [], 'order' => 2],
                    ['handle' => 'twitter', 'type' => 'url', 'display' => 'X (Twitter)', 'required' => false, 'translatable' => false, 'width' => 50, 'section' => 'main', 'config' => [], 'order' => 3],
                    ['handle' => 'youtube', 'type' => 'url', 'display' => 'YouTube', 'required' => false, 'translatable' => false, 'width' => 50, 'section' => 'main', 'config' => [], 'order' => 4],
                    ['handle' => 'linkedin', 'type' => 'url', 'display' => 'LinkedIn', 'required' => false, 'translatable' => false, 'width' => 50, 'section' => 'main', 'config' => [], 'order' => 5],
                ],
                'data' => [],
            ],
            [
                'name' => 'footer',
                'title' => 'Patička',
                'handle' => 'footer',
                'fields' => [
                    ['handle' => 'copyright_text', 'type' => 'text', 'display' => 'Copyright text', 'required' => false, 'translatable' => true, 'width' => 100, 'section' => 'main', 'config' => [], 'order' => 1],
                    ['handle' => 'show_social', 'type' => 'toggle', 'display' => 'Zobrazit sociální sítě', 'required' => false, 'translatable' => false, 'width' => 50, 'section' => 'main', 'config' => [], 'order' => 2],
                    ['handle' => 'extra_html', 'type' => 'code', 'display' => 'Extra HTML', 'required' => false, 'translatable' => false, 'width' => 100, 'section' => 'main', 'config' => [], 'order' => 3],
                ],
                'data' => [
                    'copyright_text' => '© '.date('Y').' miPress. Všechna práva vyhrazena.',
                    'show_social' => true,
                ],
            ],
        ];

        foreach ($globals as $global) {
            GlobalSet::updateOrCreate(
                ['handle' => $global['handle']],
                $global,
            );
        }
    }
}

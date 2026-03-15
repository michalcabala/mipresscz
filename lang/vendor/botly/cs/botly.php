<?php

declare(strict_types=1);

// Translations for Awcodes/Botly
return [
    'title' => 'Správa robots.txt',
    'navigation' => [
        'label' => 'Robots Manager',
    ],
    'form' => [
        'rules' => [
            'label' => 'Pravidla',
            'fields' => [
                'user_agent' => 'User-Agent',
                'directive' => 'Direktiva',
                'disallow' => 'Disallow',
                'allow' => 'Allow',
                'crawl_delay' => 'Crawl-delay',
                'clean_param' => 'Clean-param',
                'path' => 'Cesta',
            ],
            'add' => 'Přidat pravidlo',
        ],
        'sitemaps' => [
            'label' => 'Sitemapy',
            'field' => 'URL sitemapy',
            'add' => 'Přidat URL sitemapy',
        ],
        'ai_crawlers' => [
            'label' => 'Blokovat AI roboty',
        ],
        'submit' => 'Uložit',
        'callout' => [
            'label' => 'Nalezen existující soubor',
            'description' => 'Byl zjištěn existující soubor robots.txt ve veřejném adresáři. Aby tyto změny nabyly účinnosti, musíte existující soubor smazat nebo přejmenovat.',
            'delete' => 'Smazat soubor',
            'delete_success' => 'Soubor robots.txt byl úspěšně smazán.',
            'rename' => 'Přejmenovat soubor na robots-bak.txt',
            'rename_success' => 'Soubor robots.txt byl úspěšně přejmenován.',
        ],
    ],
    'export' => [
        'label' => 'Exportovat robots.txt',
        'success' => 'Robots.txt byl úspěšně exportován do public/robots.txt.',
    ],
];

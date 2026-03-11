<?php

return [
    // ── Collections ──
    'collections' => [
        'label' => 'Kolekce',
        'plural_label' => 'Kolekce',
        'navigation_label' => 'Kolekce',
        'navigation_group' => 'Struktura',
    ],

    'collection_fields' => [
        'title' => 'Název',
        'handle' => 'Handle',
        'description' => 'Popis',
        'is_tree' => 'Stromová struktura',
        'route_template' => 'Šablona URL',
        'sort_field' => 'Řadit podle',
        'sort_direction' => 'Směr řazení',
        'date_behavior' => 'Chování data',
        'default_status' => 'Výchozí stav',
        'revisions_enabled' => 'Ukládat revize',
        'icon' => 'Ikona',
        'is_active' => 'Aktivní',
        'settings' => 'Nastavení',
        'taxonomies' => 'Taxonomie',
    ],

    // ── Blueprints ──
    'blueprints' => [
        'label' => 'Blueprint',
        'plural_label' => 'Blueprinty',
        'navigation_label' => 'Blueprinty',
        'navigation_group' => 'Struktura',
    ],

    'blueprint_fields' => [
        'title' => 'Název',
        'handle' => 'Handle',
        'is_default' => 'Výchozí',
        'fields' => 'Pole',
        'collection' => 'Kolekce',
        'add_field' => 'Přidat pole',
    ],

    'field_types' => [
        'label' => 'Typ pole',
        'text' => 'Text',
        'textarea' => 'Víceřádkový text',
        'rich_editor' => 'Bohatý text',
        'mason' => 'Mason (bloky)',
        'number' => 'Číslo',
        'select' => 'Výběr',
        'toggle' => 'Přepínač',
        'curator' => 'Média',
        'entries' => 'Záznamy',
    ],

    'field_config' => [
        'instructions' => 'Nápověda',
        'section' => 'Sekce',
        'section_main' => 'Hlavní',
        'section_sidebar' => 'Postranní panel',
        'width' => 'Šířka',
        'width_half' => '1/2 šířky',
        'width_full' => 'Celá šířka',
        'required' => 'Povinné',
        'translatable' => 'Přeložitelné',
        'config' => 'Konfigurace',
    ],

    // ── Entries ──
    'entries' => [
        'label' => 'Záznam',
        'plural_label' => 'Záznamy',
        'navigation_label' => 'Záznamy',
        'navigation_group' => 'Obsah',
    ],

    'entry_fields' => [
        'title' => 'Název',
        'slug' => 'Slug',
        'slug_helper' => 'Automaticky generováno z názvu',
        'uri' => 'URI',
        'status' => 'Stav',
        'collection' => 'Kolekce',
        'blueprint' => 'Blueprint',
        'author' => 'Autor',
        'parent' => 'Nadřazený záznam',
        'origin' => 'Původní záznam',
        'locale' => 'Jazyk',
        'published_at' => 'Publikováno',
        'order' => 'Pořadí',
        'data' => 'Data',
        'content' => 'Obsah',
        'extra_fields' => 'Další pole',
        'metadata' => 'Metadata',
        'featured_image' => 'Hlavní obrázek',
        'excerpt' => 'Perex',
        'is_pinned' => 'Připnuto',
        'updated_at' => 'Upraveno',
        'publishing' => 'Publikování',
        'structure' => 'Zařazení',
        'hierarchy' => 'Hierarchie',
        'advanced' => 'Pokročilé',
        'seo' => 'SEO',
        'meta_title' => 'SEO titulek',
        'meta_title_hint' => 'Ponechte prázdné pro automatické použití názvu stránky.',
        'meta_description' => 'SEO popis',
        'meta_description_hint' => 'Krátký popis stránky pro vyhledávače (doporučeno 120–160 znaků).',
        'meta_og_image' => 'OG obrázek (sociální sítě)',
    ],

    // ── Dashboard stats ──
    'stats' => [
        'total_entries' => 'Celkem záznamů',
        'published' => 'Publikováno',
        'drafts' => 'Koncepty',
        'scheduled' => 'Naplánováno',
        'latest_entries' => 'Poslední aktivita',
    ],

    // ── Media (Curator) ──
    'media' => [
        'label' => 'Médium',
        'plural_label' => 'Média',
    ],

    // ── Taxonomies ──
    'taxonomies' => [
        'label' => 'Taxonomie',
        'plural_label' => 'Taxonomie',
        'navigation_label' => 'Taxonomie',
        'navigation_group' => 'Struktura',
    ],

    'taxonomy_fields' => [
        'title' => 'Název',
        'handle' => 'Handle',
        'is_hierarchical' => 'Hierarchická',
        'is_active' => 'Aktivní',
        'description' => 'Popis',
        'collections' => 'Kolekce',
    ],

    // ── Terms ──
    'terms' => [
        'label' => 'Termín',
        'plural_label' => 'Termíny',
        'navigation_label' => 'Termíny',
        'navigation_group' => 'Struktura',
    ],

    'term_fields' => [
        'title' => 'Název',
        'slug' => 'Slug',
        'parent' => 'Nadřazený',
        'order' => 'Pořadí',
        'data' => 'Data',
        'taxonomy' => 'Taxonomie',
    ],

    // ── Revisions ──
    'revisions' => [
        'label' => 'Revize',
        'plural_label' => 'Revize',
    ],

    'revision_fields' => [
        'entry' => 'Záznam',
        'user' => 'Uživatel',
        'data' => 'Data',
        'is_current' => 'Aktuální',
        'note' => 'Poznámka',
        'created_at' => 'Vytvořeno',
    ],

    // ── Global Sets ──
    'globals' => [
        'label' => 'Globální sada',
        'plural_label' => 'Globální sady',
        'navigation_label' => 'Globální nastavení',
        'navigation_group' => 'Struktura',
    ],

    'global_fields' => [
        'title' => 'Název',
        'handle' => 'Handle',
        'fields' => 'Pole',
        'data' => 'Data',
    ],

    // ── Entry Statuses ──
    'statuses' => [
        'draft' => 'Koncept',
        'published' => 'Publikováno',
        'scheduled' => 'Naplánováno',
        'archived' => 'Archivováno',
    ],

    // ── Date Behaviors ──
    'date_behaviors' => [
        'none' => 'Žádné',
        'required' => 'Povinné',
        'optional' => 'Volitelné',
    ],

    // ── Sort Directions ──
    'sort_directions' => [
        'asc' => 'Vzestupně',
        'desc' => 'Sestupně',
    ],

    // ── Locales ──
    'locales' => [
        'cs' => 'Čeština',
        'en' => 'Angličtina',
    ],

    // ── Actions & Messages ──
    'actions' => [
        'create_entry' => 'Vytvořit záznam',
        'edit_entry' => 'Upravit záznam',
        'delete_entry' => 'Smazat záznam',
        'restore_entry' => 'Obnovit záznam',
        'save' => 'Uložit',
        'save_draft' => 'Uložit koncept',
        'publish' => 'Publikovat',
        'unpublish' => 'Odpublikovat',
        'duplicate' => 'Duplikovat',
        'translate' => 'Přeložit',
        'create_translation' => 'Vytvořit překlad',
        'create_translation_for' => 'Vytvořit překlad — :locale',
        'add_short' => 'Přidat',
        'create_translation_confirm' => 'Bude vytvořen nový překlad pro jazyk :locale. Data budou zkopírována z původního záznamu.',
        'restore_revision' => 'Obnovit revizi',
        'view_revisions' => 'Zobrazit revize',
    ],

    'messages' => [
        'entry_created' => 'Záznam byl vytvořen.',
        'entry_updated' => 'Záznam byl aktualizován.',
        'entry_deleted' => 'Záznam byl smazán.',
        'entry_restored' => 'Záznam byl obnoven.',
        'entry_published' => 'Záznam byl publikován.',
        'revision_restored' => 'Revize byla obnovena.',
        'translation_created' => 'Překlad byl vytvořen.',
        'no_blueprint_fields' => 'Blueprint nemá definovaná žádná pole.',
    ],
];

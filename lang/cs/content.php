<?php

return [
    // ── Collections ──
    'collections' => [
        'label' => 'Kolekce',
        'plural_label' => 'Kolekce',
        'navigation_label' => 'Kolekce',
        'navigation_group' => 'Obsah',
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
    ],

    // ── Blueprints ──
    'blueprints' => [
        'label' => 'Blueprint',
        'plural_label' => 'Blueprinty',
        'navigation_label' => 'Blueprinty',
    ],

    'blueprint_fields' => [
        'title' => 'Název',
        'handle' => 'Handle',
        'is_default' => 'Výchozí',
        'fields' => 'Pole',
        'collection' => 'Kolekce',
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
    ],

    // ── Taxonomies ──
    'taxonomies' => [
        'label' => 'Taxonomie',
        'plural_label' => 'Taxonomie',
        'navigation_label' => 'Taxonomie',
        'navigation_group' => 'Obsah',
    ],

    'taxonomy_fields' => [
        'title' => 'Název',
        'handle' => 'Handle',
        'is_hierarchical' => 'Hierarchická',
        'is_active' => 'Aktivní',
        'description' => 'Popis',
    ],

    // ── Terms ──
    'terms' => [
        'label' => 'Termín',
        'plural_label' => 'Termíny',
        'navigation_label' => 'Termíny',
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

    // ── Blocks ──
    'blocks' => [
        'label' => 'Blok',
        'plural_label' => 'Bloky',
        'navigation_label' => 'Bloky',
        'navigation_group' => 'Obsah',
    ],

    'block_fields' => [
        'name' => 'Název',
        'handle' => 'Handle',
        'description' => 'Popis',
        'icon' => 'Ikona',
        'fields' => 'Pole',
        'is_active' => 'Aktivní',
    ],

    // ── Global Sets ──
    'globals' => [
        'label' => 'Globální sada',
        'plural_label' => 'Globální sady',
        'navigation_label' => 'Globální nastavení',
        'navigation_group' => 'Obsah',
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
        'publish' => 'Publikovat',
        'unpublish' => 'Odpublikovat',
        'duplicate' => 'Duplikovat',
        'translate' => 'Přeložit',
        'create_translation' => 'Vytvořit překlad',
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

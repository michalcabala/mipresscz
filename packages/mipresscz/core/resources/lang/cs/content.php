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
        'translations' => 'Překlady',
        'translations_hint' => 'Lokalizované názvy pro jednotlivé jazyky (volitelné — pokud není vyplněno, použije se výchozí název).',
        'language' => 'Jazyk',
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
        'use_mason' => 'Použít Mason editor',
        'use_mason_hint' => 'Zapne editor pro tvorbu obsahu z bloků (Mason). Vhodné pro stránky a obsah se strukturovaným layoutem.',
        'fields' => 'Pole',
        'collection' => 'Kolekce',
        'add_field' => 'Přidat pole',
    ],

    'field_types' => [
        'label' => 'Typ pole',
        // Textový obsah
        'text' => 'Text',
        'textarea' => 'Víceřádkový text',
        'rich_editor' => 'Bohatý text',
        'mason' => 'Mason (bloky)',
        'markdown' => 'Markdown',
        // Číslo a výběr
        'number' => 'Číslo',
        'select' => 'Výběr',
        'toggle' => 'Přepínač (ano/ne)',
        'checkbox' => 'Zaškrtávací pole',
        'radio' => 'Výběr jedné možnosti',
        'checkbox_list' => 'Výběr více možností',
        // Datum a čas
        'date' => 'Datum',
        'datetime' => 'Datum a čas',
        'time' => 'Čas',
        // Kontakt a web
        'email' => 'E-mail',
        'url' => 'URL adresa',
        // Média
        'curator' => 'Média',
        'color' => 'Barva',
        'tags' => 'Štítky',
        // Relace
        'entries' => 'Záznamy',
    ],

    'field_type_groups' => [
        'text' => 'Textový obsah',
        'selection' => 'Číslo a výběr',
        'date_time' => 'Datum a čas',
        'contact' => 'Kontakt a web',
        'media' => 'Média',
        'relations' => 'Relace',
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
        'image' => 'Obrázek',
        'content' => 'Obsah',
        'extra_fields' => 'Další pole',
        'metadata' => 'Metadata',
        'featured_image' => 'Náhledový obrázek',
        'excerpt' => 'Perex',
        'is_pinned' => 'Připnuto',
        'is_homepage' => 'Domovská stránka',
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
        'computed_data' => 'Statistiky obsahu',
        'word_count' => 'Počet slov',
        'reading_time' => 'Doba čtení',
        'reading_time_value' => ':minutes min',
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

    // ── Media Folders ──
    'media_folders' => [
        'label' => 'Složka médií',
        'plural_label' => 'Složky médií',
        'navigation_label' => 'Složky',
    ],

    'media_folder_fields' => [
        'name' => 'Název',
        'slug' => 'Slug',
        'parent' => 'Nadřazená složka',
        'order' => 'Pořadí',
        'media_count' => 'Počet médií',
    ],

    // ── Media Tags ──
    'media_tags' => [
        'label' => 'Štítek médií',
        'plural_label' => 'Štítky médií',
        'navigation_label' => 'Štítky',
    ],

    'media_tag_fields' => [
        'name' => 'Název',
        'slug' => 'Slug',
        'media_count' => 'Počet médií',
        'folder' => 'Složka',
        'tags' => 'Štítky',
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
        'translations' => 'Překlady',
        'translations_hint' => 'Lokalizované názvy pro jednotlivé jazyky (volitelné).',
        'language' => 'Jazyk',
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
        'origin' => 'Zdrojový termín (překlad z)',
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
        'content' => 'Obsah',
        'is_current' => 'Aktuální',
        'note' => 'Poznámka',
        'created_at' => 'Vytvořeno',
        'action' => 'Akce',
        'action_revision' => 'Revize',
        'action_working' => 'Rozpracováno',
        'action_publish' => 'Publikace',
        'action_unpublish' => 'Odpublikování',
        'before' => 'Před',
        'after' => 'Po',
        'no_changes' => 'Žádné změny.',
        'no_previous_revision' => 'Toto je nejstarší revize – není s čím porovnat.',
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
        'set_homepage' => 'Nastavit jako domovskou stránku',
        'is_homepage' => 'Domovská stránka',
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
        'preview' => 'Náhled',
        'view_revision' => 'Detail revize',
        'compare_revision' => 'Porovnat s předchozí',
        'restore_revision' => 'Obnovit revizi',
        'view_revisions' => 'Zobrazit revize',
        'publish_changes' => 'Publikovat změny',
        'discard_changes' => 'Zahodit změny',
        'discard_changes_confirm' => 'Opravdu chcete zahodit rozpracované změny? Tato akce je nevratná.',
    ],

    // ── Menus ──
    'menus' => [
        'label' => 'Navigační menu',
        'plural_label' => 'Navigační menu',
        'navigation_label' => 'Navigační menu',
        'navigation_group' => 'Navigace',
        'manage_items' => 'Spravovat položky',
        'locations' => [
            'primary' => 'Hlavní navigace',
            'footer' => 'Patička',
        ],

        // Page
        'saved' => 'Uloženo',
        'create_menu' => 'Vytvořit menu',
        'delete_menu' => 'Smazat menu',
        'location' => 'Umístění',
        'name' => 'Název',
        'created_notification' => 'Menu bylo vytvořeno.',
        'deleted_notification' => 'Menu bylo smazáno.',

        // Builder
        'no_menu_selected' => 'Žádné menu není vybráno',
        'no_menu_hint' => 'Vyberte umístění a menu nebo vytvořte nové.',
        'items_heading' => 'Položky menu',
        'auto_save_on' => 'Auto-ukládání zapnuto',
        'add_items_hint' => 'Přidejte první položku z panelu vpravo.',
        'delete_item_title' => 'Smazat položku',
        'delete_item_confirm' => 'Opravdu chcete tuto položku smazat? Budou smazány i všechny podpoložky.',
        'cancel' => 'Zrušit',
        'delete' => 'Smazat',

        // Menu item
        'drag_to_reorder' => 'Přetáhněte pro změnu pořadí',
        'hidden' => 'Skryté',
        'move_up' => 'Posunout výš',
        'move_down' => 'Posunout níž',
        'indent' => 'Odsadit',
        'outdent' => 'Předsadit',
        'disable' => 'Skrýt',
        'enable' => 'Zobrazit',
        'edit' => 'Upravit',
        'remove' => 'Odebrat',
        'field_title' => 'Název',
        'field_title_placeholder' => 'Název položky',
        'field_url' => 'URL',
        'field_target' => 'Otevřít v',
        'target_self' => 'Stejná záložka',
        'target_blank' => 'Nová záložka',
        'field_visible' => 'Viditelné',
        'save' => 'Uložit',

        // Panel
        'panel_tab_custom' => 'Vlastní odkaz',
        'panel_tab_models' => 'Záznamy',
        'panel_tab_archives' => 'Archivy',
        'select_menu_first' => 'Nejdříve vyberte menu.',
        'custom_title_placeholder' => 'Název odkazu',
        'or' => 'nebo',
        'field_open_in' => 'Otevřít v',
        'add_to_menu' => 'Přidat do menu',
        'no_model_sources' => 'Nejsou nakonfigurované žádné zdroje záznamů.',
        'no_archive_sources' => 'Nejsou dostupné žádné archivní zdroje.',
        'search' => 'Hledat',
        'search_placeholder' => 'Hledat záznamy…',
        'already_added' => 'Již přidáno',
        'no_records' => 'Žádné záznamy.',
        'type_custom' => 'Vlastní odkaz',
        'type_model' => 'Záznam',
        'type_archive' => 'Archiv',
    ],

    'messages' => [
        'entry_created' => 'Záznam byl vytvořen.',
        'entry_updated' => 'Záznam byl aktualizován.',
        'entry_deleted' => 'Záznam byl smazán.',
        'entry_restored' => 'Záznam byl obnoven.',
        'homepage_set' => 'Domovská stránka byla nastavena.',
        'cannot_delete_homepage' => 'Nelze smazat záznam nastavený jako domovská stránka.',
        'taxonomies_already_assigned' => 'Tyto taxonomie už jsou přiřazené k jiné kolekci: :taxonomies',
        'entry_published' => 'Záznam byl publikován.',
        'revision_restored' => 'Revize byla obnovena.',
        'revision_loaded_to_working_copy' => 'Revize byla načtena do rozpracované verze.',
        'translation_created' => 'Překlad byl vytvořen.',
        'no_blueprint_fields' => 'Blueprint nemá definovaná žádná pole.',
        'working_copy_saved' => 'Změny byly uloženy jako rozpracovaná verze.',
        'working_copy_published' => 'Rozpracované změny byly publikovány.',
        'working_copy_discarded' => 'Rozpracované změny byly zahozeny.',
    ],

    // ── Search ──
    'search' => [
        'title' => 'Vyhledávání',
        'placeholder' => 'Hledejte na webu…',
        'button' => 'Hledat',
        'no_results' => 'Pro dotaz „:query" nebyly nalezeny žádné výsledky.',
        'results_count' => '{1} :count výsledek pro „:query"|[2,4] :count výsledky pro „:query"|[5,*] :count výsledků pro „:query"',
        'min_length' => 'Zadejte alespoň 2 znaky.',
    ],
];

<?php

return [
    'yes' => 'Yes',
    'no' => 'No',

    // ── Collections ──
    'collections' => [
        'label' => 'Collection',
        'plural_label' => 'Collections',
        'navigation_label' => 'Collections',
        'navigation_group' => 'Structure',
    ],

    'collection_fields' => [
        'title' => 'Title',
        'handle' => 'Handle',
        'description' => 'Description',
        'is_tree' => 'Tree structure',
        'route_template' => 'URL template',
        'sort_field' => 'Sort by',
        'sort_direction' => 'Sort direction',
        'date_behavior' => 'Date behavior',
        'default_status' => 'Default status',
        'icon' => 'Icon',
        'is_active' => 'Active',
        'settings' => 'Settings',
        'taxonomies' => 'Taxonomies',
        'translations' => 'Translations',
        'translations_hint' => 'Localized names for individual languages (optional — if empty, the default title will be used).',
        'language' => 'Language',
    ],

    // ── Blueprints ──
    'blueprints' => [
        'label' => 'Blueprint',
        'plural_label' => 'Blueprints',
        'navigation_label' => 'Blueprints',
        'navigation_group' => 'Structure',
    ],

    'blueprint_fields' => [
        'title' => 'Title',
        'handle' => 'Handle',
        'is_default' => 'Default',
        'use_mason' => 'Use Mason editor',
        'use_mason_hint' => 'Enables the block-based content editor (Mason). Recommended for pages and content with structured layouts.',
        'fields' => 'Fields',
        'collection' => 'Collection',
        'add_field' => 'Add field',
    ],

    'field_types' => [
        'label' => 'Field type',
        // Text content
        'text' => 'Text',
        'textarea' => 'Textarea',
        'rich_editor' => 'Rich editor',
        'mason' => 'Mason (blocks)',
        'markdown' => 'Markdown',
        // Number & selection
        'number' => 'Number',
        'select' => 'Select',
        'toggle' => 'Toggle (yes/no)',
        'checkbox' => 'Checkbox',
        'radio' => 'Radio',
        'checkbox_list' => 'Checkbox list',
        // Date & time
        'date' => 'Date',
        'datetime' => 'Date & time',
        'time' => 'Time',
        // Contact & links
        'email' => 'Email',
        'url' => 'URL',
        // Media
        'curator' => 'Media',
        'color' => 'Color',
        'tags' => 'Tags',
        // Relations
        'entries' => 'Entries',
    ],

    'field_type_groups' => [
        'text' => 'Text content',
        'selection' => 'Number & selection',
        'date_time' => 'Date & time',
        'contact' => 'Contact & links',
        'media' => 'Media',
        'relations' => 'Relations',
    ],

    'field_config' => [
        'instructions' => 'Instructions',
        'section' => 'Section',
        'section_main' => 'Main',
        'section_sidebar' => 'Sidebar',
        'width' => 'Width',
        'width_half' => '1/2 width',
        'width_full' => 'Full width',
        'required' => 'Required',
        'translatable' => 'Translatable',
        'config' => 'Configuration',
    ],

    // ── Entries ──
    'entries' => [
        'label' => 'Entry',
        'plural_label' => 'Entries',
        'navigation_label' => 'Entries',
        'navigation_group' => 'Content',
    ],

    'entry_fields' => [
        'title' => 'Title',
        'slug' => 'Slug',
        'slug_helper' => 'Auto-generated from title',
        'uri' => 'URI',
        'status' => 'Status',
        'collection' => 'Collection',
        'blueprint' => 'Blueprint',
        'author' => 'Author',
        'parent' => 'Parent entry',
        'origin' => 'Origin entry',
        'locale' => 'Locale',
        'published_at' => 'Published at',
        'order' => 'Order',
        'data' => 'Data',
        'image' => 'Image',
        'content' => 'Content',
        'extra_fields' => 'Additional fields',
        'metadata' => 'Metadata',
        'featured_image' => 'Featured image',
        'excerpt' => 'Excerpt',
        'is_pinned' => 'Pinned',
        'is_homepage' => 'Homepage',
        'updated_at' => 'Updated',
        'publishing' => 'Publishing',
        'structure' => 'Organization',
        'hierarchy' => 'Hierarchy',
        'advanced' => 'Advanced',
        'seo' => 'SEO',
        'meta_title' => 'SEO title',
        'meta_title_hint' => 'Leave blank to use the page title automatically.',
        'meta_description' => 'SEO description',
        'meta_description_hint' => 'Short description for search engines (recommended 120–160 characters).',
        'meta_og_image' => 'OG image (social media)',
        'computed_data' => 'Content statistics',
        'word_count' => 'Word count',
        'reading_time' => 'Reading time',
        'reading_time_value' => ':minutes min',
    ],

    // ── Dashboard stats ──
    'stats' => [
        'total_entries' => 'Total entries',
        'published' => 'Published',
        'drafts' => 'Drafts',
        'scheduled' => 'Scheduled',
        'latest_entries' => 'Latest activity',
    ],

    // ── Media (Curator) ──
    'media' => [
        'label' => 'Medium',
        'plural_label' => 'Media',
    ],

    // ── Media Folders ──
    'media_folders' => [
        'label' => 'Media Folder',
        'plural_label' => 'Media Folders',
        'navigation_label' => 'Folders',
    ],

    'media_folder_fields' => [
        'name' => 'Name',
        'slug' => 'Slug',
        'parent' => 'Parent Folder',
        'order' => 'Order',
        'media_count' => 'Media Count',
    ],

    // ── Media Tags ──
    'media_tags' => [
        'label' => 'Media Tag',
        'plural_label' => 'Media Tags',
        'navigation_label' => 'Tags',
    ],

    'media_tag_fields' => [
        'name' => 'Name',
        'slug' => 'Slug',
        'media_count' => 'Media Count',
        'folder' => 'Folder',
        'tags' => 'Tags',
    ],

    // ── Taxonomies ──
    'taxonomies' => [
        'label' => 'Taxonomy',
        'plural_label' => 'Taxonomies',
        'navigation_label' => 'Taxonomies',
        'navigation_group' => 'Structure',
    ],

    'taxonomy_fields' => [
        'title' => 'Title',
        'handle' => 'Handle',
        'is_hierarchical' => 'Hierarchical',
        'is_active' => 'Active',
        'description' => 'Description',
        'collections' => 'Collections',
        'translations' => 'Translations',
        'translations_hint' => 'Localized names for individual languages (optional).',
        'language' => 'Language',
    ],

    // ── Terms ──
    'terms' => [
        'label' => 'Term',
        'plural_label' => 'Terms',
        'navigation_label' => 'Terms',
        'navigation_group' => 'Structure',
    ],

    'term_fields' => [
        'title' => 'Title',
        'slug' => 'Slug',
        'parent' => 'Parent',
        'order' => 'Order',
        'data' => 'Data',
        'taxonomy' => 'Taxonomy',
        'origin' => 'Origin term (translation of)',
    ],

    // ── Global Sets ──
    'globals' => [
        'label' => 'Global Set',
        'plural_label' => 'Global Sets',
        'navigation_label' => 'Global Settings',
        'navigation_group' => 'Structure',
    ],

    'global_fields' => [
        'title' => 'Title',
        'handle' => 'Handle',
        'fields' => 'Fields',
        'data' => 'Data',
    ],

    // ── Entry Statuses ──
    'statuses' => [
        'draft' => 'Draft',
        'published' => 'Published',
        'scheduled' => 'Scheduled',
        'archived' => 'Archived',
    ],

    // ── Date Behaviors ──
    'date_behaviors' => [
        'none' => 'None',
        'required' => 'Required',
        'optional' => 'Optional',
    ],

    // ── Sort Directions ──
    'sort_directions' => [
        'asc' => 'Ascending',
        'desc' => 'Descending',
    ],

    // ── Locales ──
    'locales' => [
        'cs' => 'Czech',
        'en' => 'English',
    ],

    // ── Actions & Messages ──
    'actions' => [
        'set_homepage' => 'Set as homepage',
        'is_homepage' => 'Homepage',
        'create_entry' => 'Create entry',
        'edit_entry' => 'Edit entry',
        'delete_entry' => 'Delete entry',
        'restore_entry' => 'Restore entry',
        'save' => 'Save',
        'save_draft' => 'Save as draft',
        'publish' => 'Publish',
        'unpublish' => 'Unpublish',
        'duplicate' => 'Duplicate',
        'translate' => 'Translate',
        'create_translation' => 'Create translation',
        'create_translation_for' => 'Create translation — :locale',
        'add_short' => 'Add',
        'create_translation_confirm' => 'A new translation for :locale will be created. Data will be copied from the original entry.',
        'preview' => 'Preview',
        'more_actions' => 'More',
        'discard_changes' => 'Discard changes',
        'discard_changes_confirm' => 'Are you sure you want to discard the current form changes? This action cannot be undone.',
    ],

    // ── Menus ──
    'menus' => [
        'label' => 'Menu',
        'plural_label' => 'Menus',
        'navigation_label' => 'Menus',
        'navigation_group' => 'Navigation',
        'manage_items' => 'Manage items',
        'locations' => [
            'primary' => 'Primary navigation',
            'footer' => 'Footer',
        ],

        // Page
        'saved' => 'Saved',
        'create_menu' => 'Create menu',
        'delete_menu' => 'Delete menu',
        'location' => 'Location',
        'name' => 'Name',
        'created_notification' => 'Menu has been created.',
        'deleted_notification' => 'Menu has been deleted.',

        // Builder
        'no_menu_selected' => 'No menu selected',
        'no_menu_hint' => 'Select a location and menu or create a new one.',
        'items_heading' => 'Menu items',
        'auto_save_on' => 'Auto-save enabled',
        'add_items_hint' => 'Add your first item from the panel on the right.',
        'delete_item_title' => 'Delete item',
        'delete_item_confirm' => 'Are you sure you want to delete this item? All child items will also be removed.',
        'cancel' => 'Cancel',
        'delete' => 'Delete',

        // Menu item
        'drag_to_reorder' => 'Drag to reorder',
        'hidden' => 'Hidden',
        'move_up' => 'Move up',
        'move_down' => 'Move down',
        'indent' => 'Indent',
        'outdent' => 'Outdent',
        'disable' => 'Hide',
        'enable' => 'Show',
        'edit' => 'Edit',
        'remove' => 'Remove',
        'field_title' => 'Title',
        'field_title_placeholder' => 'Item title',
        'field_url' => 'URL',
        'field_target' => 'Open in',
        'target_self' => 'Same tab',
        'target_blank' => 'New tab',
        'field_visible' => 'Visible',
        'save' => 'Save',

        // Panel
        'panel_tab_custom' => 'Custom link',
        'panel_tab_models' => 'Records',
        'panel_tab_archives' => 'Archives',
        'select_menu_first' => 'Select a menu first.',
        'custom_title_placeholder' => 'Link title',
        'or' => 'or',
        'field_open_in' => 'Open in',
        'add_to_menu' => 'Add to menu',
        'no_model_sources' => 'No model sources are configured.',
        'no_archive_sources' => 'No archive sources are available.',
        'search' => 'Search',
        'search_placeholder' => 'Search records…',
        'already_added' => 'Already added',
        'no_records' => 'No records found.',
        'type_custom' => 'Custom link',
        'type_model' => 'Record',
        'type_archive' => 'Archive',
    ],

    'messages' => [
        'entry_created' => 'Entry created.',
        'entry_updated' => 'Entry updated.',
        'entry_deleted' => 'Entry deleted.',
        'entry_restored' => 'Entry restored.',
        'homepage_set' => 'Homepage has been set.',
        'cannot_delete_homepage' => 'The homepage entry cannot be deleted.',
        'taxonomies_already_assigned' => 'These taxonomies are already assigned to another collection: :taxonomies',
        'entry_published' => 'Entry published.',
        'translation_created' => 'Translation created.',
        'no_blueprint_fields' => 'Blueprint has no fields defined.',
    ],

    // ── Search ──
    'search' => [
        'title' => 'Search',
        'placeholder' => 'Search the site…',
        'button' => 'Search',
        'no_results' => 'No results found for ":query".',
        'results_count' => '{1} :count result for ":query"|[2,*] :count results for ":query"',
        'min_length' => 'Enter at least 2 characters.',
    ],
];

<?php

return [
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
        'revisions_enabled' => 'Save revisions',
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
        'text' => 'Text',
        'textarea' => 'Textarea',
        'rich_editor' => 'Rich editor',
        'mason' => 'Mason (blocks)',
        'number' => 'Number',
        'select' => 'Select',
        'toggle' => 'Toggle',
        'curator' => 'Media',
        'entries' => 'Entries',
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

    // ── Revisions ──
    'revisions' => [
        'label' => 'Revision',
        'plural_label' => 'Revisions',
    ],

    'revision_fields' => [
        'entry' => 'Entry',
        'user' => 'User',
        'data' => 'Data',
        'is_current' => 'Current',
        'note' => 'Note',
        'created_at' => 'Created at',
        'action' => 'Action',
        'action_revision' => 'Revision',
        'action_working' => 'Working copy',
        'action_publish' => 'Publish',
        'action_unpublish' => 'Unpublish',
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
        'restore_revision' => 'Restore revision',
        'view_revisions' => 'View revisions',
        'publish_changes' => 'Publish changes',
        'discard_changes' => 'Discard changes',
        'discard_changes_confirm' => 'Are you sure you want to discard the working copy? This action cannot be undone.',
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
    ],

    'menu_fields' => [
        'title' => 'Title',
        'handle' => 'Handle',
        'location' => 'Location',
        'description' => 'Description',
    ],

    'menu_locations' => [
        'primary' => 'Primary navigation',
        'footer' => 'Footer',
        'sidebar' => 'Sidebar',
    ],

    'menu_item_fields' => [
        'type' => 'Type',
        'title' => 'Label',
        'url' => 'URL',
        'entry' => 'Entry',
        'target' => 'Open in',
        'is_active' => 'Active',
    ],

    'menu_item_types' => [
        'custom_link' => 'Custom link',
        'entry' => 'Entry',
    ],

    'menu_item_targets' => [
        '_self' => 'Same tab',
        '_blank' => 'New tab',
    ],

    'messages' => [
        'entry_created' => 'Entry created.',
        'entry_updated' => 'Entry updated.',
        'entry_deleted' => 'Entry deleted.',
        'entry_restored' => 'Entry restored.',
        'homepage_set' => 'Homepage has been set.',
        'cannot_delete_homepage' => 'The homepage entry cannot be deleted.',
        'entry_published' => 'Entry published.',
        'revision_restored' => 'Revision restored.',
        'translation_created' => 'Translation created.',
        'no_blueprint_fields' => 'Blueprint has no fields defined.',
        'working_copy_saved' => 'Changes saved as working copy.',
        'working_copy_published' => 'Working copy has been published.',
        'working_copy_discarded' => 'Working copy has been discarded.',
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

<?php

return [
    // ── Collections ──
    'collections' => [
        'label' => 'Collection',
        'plural_label' => 'Collections',
        'navigation_label' => 'Collections',
        'navigation_group' => 'Content',
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
    ],

    // ── Blueprints ──
    'blueprints' => [
        'label' => 'Blueprint',
        'plural_label' => 'Blueprints',
        'navigation_label' => 'Blueprints',
    ],

    'blueprint_fields' => [
        'title' => 'Title',
        'handle' => 'Handle',
        'is_default' => 'Default',
        'fields' => 'Fields',
        'collection' => 'Collection',
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
    ],

    // ── Taxonomies ──
    'taxonomies' => [
        'label' => 'Taxonomy',
        'plural_label' => 'Taxonomies',
        'navigation_label' => 'Taxonomies',
        'navigation_group' => 'Content',
    ],

    'taxonomy_fields' => [
        'title' => 'Title',
        'handle' => 'Handle',
        'is_hierarchical' => 'Hierarchical',
        'is_active' => 'Active',
        'description' => 'Description',
    ],

    // ── Terms ──
    'terms' => [
        'label' => 'Term',
        'plural_label' => 'Terms',
        'navigation_label' => 'Terms',
    ],

    'term_fields' => [
        'title' => 'Title',
        'slug' => 'Slug',
        'parent' => 'Parent',
        'order' => 'Order',
        'data' => 'Data',
        'taxonomy' => 'Taxonomy',
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
    ],

    // ── Blocks ──
    'blocks' => [
        'label' => 'Block',
        'plural_label' => 'Blocks',
        'navigation_label' => 'Blocks',
        'navigation_group' => 'Content',
    ],

    'block_fields' => [
        'name' => 'Name',
        'handle' => 'Handle',
        'description' => 'Description',
        'icon' => 'Icon',
        'fields' => 'Fields',
        'is_active' => 'Active',
    ],

    // ── Global Sets ──
    'globals' => [
        'label' => 'Global Set',
        'plural_label' => 'Global Sets',
        'navigation_label' => 'Global Settings',
        'navigation_group' => 'Content',
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
        'create_entry' => 'Create entry',
        'edit_entry' => 'Edit entry',
        'delete_entry' => 'Delete entry',
        'restore_entry' => 'Restore entry',
        'publish' => 'Publish',
        'unpublish' => 'Unpublish',
        'duplicate' => 'Duplicate',
        'translate' => 'Translate',
        'create_translation' => 'Create translation',
        'restore_revision' => 'Restore revision',
        'view_revisions' => 'View revisions',
    ],

    'messages' => [
        'entry_created' => 'Entry created.',
        'entry_updated' => 'Entry updated.',
        'entry_deleted' => 'Entry deleted.',
        'entry_restored' => 'Entry restored.',
        'entry_published' => 'Entry published.',
        'revision_restored' => 'Revision restored.',
        'translation_created' => 'Translation created.',
        'no_blueprint_fields' => 'Blueprint has no fields defined.',
    ],
];

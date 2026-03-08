<?php

return [
    'navigation_label' => 'Languages',
    'navigation_group' => 'Settings',
    'page_title' => 'Manage Languages',
    'add_locale' => 'Add Language',
    'created' => 'Language created.',
    'updated' => 'Language updated.',
    'set_as_default' => 'Set as default',
    'set_as_default_success' => 'Default language has been updated.',
    'cannot_delete_default' => 'The default language cannot be deleted.',
    'no_prefix' => '(default)',
    'url_prefix_help' => 'Empty = default locale without prefix',
    'url_prefix_single_locale_hint' => 'Prefix is not used — only one frontend language is active.',
    'language_switcher' => 'Language selector',

    'form_sections' => [
        'general' => 'General',
        'url' => 'URL & formatting',
        'availability' => 'Availability',
    ],

    'fields' => [
        'code' => 'Code',
        'name' => 'Name (in English)',
        'native_name' => 'Native name',
        'flag' => 'Flag (filename)',
        'is_default' => 'Default',
        'is_active' => 'Active',
        'is_admin_available' => 'Available in admin',
        'is_frontend_available' => 'Available on frontend',
        'direction' => 'Text direction',
        'date_format' => 'Date format',
        'url_prefix' => 'URL prefix',
        'fallback_locale' => 'Fallback locale',
    ],
];

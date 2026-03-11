<?php

return [
    'label' => 'Uživatel',
    'plural_label' => 'Uživatelé',
    'navigation_label' => 'Uživatelé',
    'navigation_group' => 'Správa',

    'fields' => [
        'name' => 'Jméno',
        'email' => 'E-mailová adresa',
        'email_verified_at' => 'E-mail ověřen',
        'password' => 'Heslo',
        'role' => 'Role',
        'deleted_at' => 'Smazáno',
        'created_at' => 'Vytvořeno',
        'updated_at' => 'Upraveno',
    ],

    'messages' => [
        'cannot_delete_self' => 'Nemůžete smazat vlastní účet.',
    ],

    'pages' => [
        'list' => 'Uživatelé',
        'create' => 'Vytvořit uživatele',
        'edit' => 'Upravit uživatele',
    ],
];

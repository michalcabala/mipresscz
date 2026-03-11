<?php

return [
    'label' => 'User',
    'plural_label' => 'Users',
    'navigation_label' => 'Users',
    'navigation_group' => 'Management',

    'fields' => [
        'name' => 'Name',
        'email' => 'Email address',
        'email_verified_at' => 'Email verified at',
        'password' => 'Password',
        'role' => 'Role',
        'deleted_at' => 'Deleted at',
        'created_at' => 'Created at',
        'updated_at' => 'Updated at',
    ],

    'messages' => [
        'cannot_delete_self' => 'You cannot delete your own account.',
    ],

    'pages' => [
        'list' => 'Users',
        'create' => 'Create user',
        'edit' => 'Edit user',
    ],
];

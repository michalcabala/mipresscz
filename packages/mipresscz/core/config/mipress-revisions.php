<?php

use MiPressCz\Core\Models\Entry;

return [
    /*
    |--------------------------------------------------------------------------
    | Maximum Revisions
    |--------------------------------------------------------------------------
    |
    | Maximum number of revisions to keep per revisionable model. Set to 0 to
    | disable pruning entirely.
    |
    */
    'max_revisions' => 50,

    /*
    |--------------------------------------------------------------------------
    | Autosave Interval
    |--------------------------------------------------------------------------
    |
    | Number of seconds between autosave polling requests on the entry edit
    | screen.
    |
    */
    'autosave_interval' => 60,

    /*
    |--------------------------------------------------------------------------
    | Autosave Limit
    |--------------------------------------------------------------------------
    |
    | Maximum number of autosave revisions to keep per revisionable model.
    | Set to 0 to disable autosave pruning.
    |
    */
    'autosave_max' => 10,

    /*
    |--------------------------------------------------------------------------
    | Preserve Published Revisions
    |--------------------------------------------------------------------------
    |
    | When enabled, published revisions are never pruned by the automatic
    | revision limit or the prune command.
    |
    */
    'prune_keep_published' => true,

    /*
    |--------------------------------------------------------------------------
    | Enabled Revisionable Models
    |--------------------------------------------------------------------------
    |
    | Models that should be included by the prune command when no --model
    | option is provided.
    |
    */
    'enabled_models' => [
        Entry::class,
    ],
];

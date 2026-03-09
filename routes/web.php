<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// CMS frontend entry routes (locale-prefixed + catch-all) are registered
// automatically by MiPressCzCoreServiceProvider via loadRoutesFrom().
// Add app-specific routes above this comment to override core behaviour.

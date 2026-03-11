<?php

use Illuminate\Support\Facades\Route;
use MiPressCz\Core\Http\Controllers\EntryController;
use MiPressCz\Core\Http\Middleware\SetFrontendLocale;

// Locale-prefixed routes for non-default languages
Route::prefix('{locale}')
    ->where(['locale' => '[a-z]{2}'])
    ->middleware([SetFrontendLocale::class])
    ->group(function () {
        Route::get('{uri}', [EntryController::class, 'show'])
            ->where('uri', '.*')
            ->name('entry.show.locale');
    });

// Default locale (no prefix) — must be last
Route::middleware([SetFrontendLocale::class])
    ->group(function () {
        Route::get('{uri}', [EntryController::class, 'show'])
            ->where('uri', '.*')
            ->name('entry.show');
    });

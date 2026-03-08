<?php

use App\Http\Controllers\EntryController;
use App\Http\Middleware\SetFrontendLocale;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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

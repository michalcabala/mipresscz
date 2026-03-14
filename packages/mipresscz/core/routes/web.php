<?php

use Illuminate\Support\Facades\Route;
use MiPressCz\Core\Http\Controllers\EntryController;
use MiPressCz\Core\Http\Controllers\FeedController;
use MiPressCz\Core\Http\Controllers\PreviewController;
use MiPressCz\Core\Http\Controllers\SearchController;
use MiPressCz\Core\Http\Controllers\SitemapController;
use MiPressCz\Core\Http\Middleware\SetFrontendLocale;

// ── Static SEO endpoints (must be before catch-all routes) ──

Route::get('sitemap.xml', [SitemapController::class, 'index'])
    ->name('sitemap');

Route::middleware([SetFrontendLocale::class])
    ->group(function () {
        Route::get('feed.xml', [FeedController::class, 'index'])
            ->name('feed');
    });

Route::prefix('{locale}')
    ->where(['locale' => '[a-z]{2}'])
    ->middleware([SetFrontendLocale::class])
    ->group(function () {
        Route::get('feed.xml', [FeedController::class, 'index'])
            ->name('feed.locale');
    });

// ── Preview route (must be before catch-all) ──

Route::get('_preview/{token}', [PreviewController::class, 'show'])
    ->where('token', '[A-Za-z0-9]{64}')
    ->name('entry.preview');

// ── Search route ──

Route::middleware([SetFrontendLocale::class])
    ->group(function () {
        Route::get('search', SearchController::class)->name('search');
    });

Route::prefix('{locale}')
    ->where(['locale' => '[a-z]{2}'])
    ->middleware([SetFrontendLocale::class])
    ->group(function () {
        Route::get('search', SearchController::class)->name('search.locale');
    });

// ── Entry catch-all routes ──

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

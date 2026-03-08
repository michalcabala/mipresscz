<?php

use App\Http\Controllers\EntryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Catch-all route for CMS entries — must be last
Route::get('{uri}', [EntryController::class, 'show'])
    ->where('uri', '.*')
    ->name('entry.show');

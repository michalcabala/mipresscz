<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('filament-sitemap-generator:generate --no-interaction')
    ->dailyAt('02:00')
    ->withoutOverlapping();

if ((int) config('mipress-revisions.max_revisions', 50) !== 0) {
    Schedule::command('mipress:prune-revisions', [
        '--keep' => (int) config('mipress-revisions.max_revisions', 50),
    ])
        ->dailyAt('03:00')
        ->withoutOverlapping();
}

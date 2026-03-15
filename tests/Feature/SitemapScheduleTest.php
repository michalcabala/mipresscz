<?php

use Illuminate\Console\Scheduling\Schedule;

it('registers sitemap generation command in scheduler', function () {
    $events = app(Schedule::class)->events();

    $hasSitemapSchedule = collect($events)->contains(
        static fn ($event): bool => str_contains((string) ($event->command ?? ''), 'filament-sitemap-generator:generate')
            && str_contains((string) ($event->command ?? ''), '--no-interaction')
    );

    expect($hasSitemapSchedule)->toBeTrue();
});

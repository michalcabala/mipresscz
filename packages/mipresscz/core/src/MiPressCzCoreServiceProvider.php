<?php

namespace MiPressCz\Core;

use Illuminate\Support\ServiceProvider;
use MiPressCz\Core\Services\LocaleService;

class MiPressCzCoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LocaleService::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'mipresscz-core');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'mipresscz-migrations');
        }
    }
}

<?php

namespace MiPressCz\Core;

use Illuminate\Support\ServiceProvider;

class MiPressCzCoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'mipresscz-migrations');
        }
    }
}

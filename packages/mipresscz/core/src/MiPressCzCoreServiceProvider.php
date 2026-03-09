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
        if (is_dir(__DIR__.'/../database/migrations')) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }
}

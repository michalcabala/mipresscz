<?php

namespace MiPressCz\Core;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\GlobalSet;
use MiPressCz\Core\Models\Taxonomy;
use MiPressCz\Core\Policies\BlueprintPolicy;
use MiPressCz\Core\Policies\CollectionPolicy;
use MiPressCz\Core\Policies\EntryPolicy;
use MiPressCz\Core\Policies\GlobalSetPolicy;
use MiPressCz\Core\Policies\TaxonomyPolicy;
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

        // Register core lang files as additional base-namespace paths so that
        // __('content.*') and __('locales.*') resolve from the package when not
        // overridden by the host application's own lang directory.
        $this->app->make('translation.loader')->addPath(__DIR__.'/../resources/lang');

        Gate::policy(Blueprint::class, BlueprintPolicy::class);
        Gate::policy(Collection::class, CollectionPolicy::class);
        Gate::policy(Entry::class, EntryPolicy::class);
        Gate::policy(GlobalSet::class, GlobalSetPolicy::class);
        Gate::policy(Taxonomy::class, TaxonomyPolicy::class);

        // Core catch-all frontend routes are registered after the application's
        // own routes so that app-specific routes always take precedence.
        // $this->app->booted() fires after withRouting() loads web routes.
        $this->app->booted(function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'mipresscz-migrations');

            $this->publishes([
                __DIR__.'/../resources/lang' => lang_path(),
            ], 'mipresscz-translations');
        }
    }
}

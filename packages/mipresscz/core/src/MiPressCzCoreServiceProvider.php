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

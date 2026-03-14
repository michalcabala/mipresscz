<?php

namespace MiPressCz\Core;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use MiPressCz\Core\Console\Commands\InstallCommand;
use MiPressCz\Core\Console\Commands\TemplateListCommand;
use MiPressCz\Core\Listeners\CacheInvalidationSubscriber;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\GlobalSet;
use MiPressCz\Core\Models\Locale;
use MiPressCz\Core\Models\MediaFolder;
use MiPressCz\Core\Models\MediaTag;
use MiPressCz\Core\Models\Taxonomy;
use MiPressCz\Core\Models\Term;
use MiPressCz\Core\Observers\EntryObserver;
use MiPressCz\Core\Observers\LocaleObserver;
use MiPressCz\Core\Policies\BlueprintPolicy;
use MiPressCz\Core\Policies\CollectionPolicy;
use MiPressCz\Core\Policies\EntryPolicy;
use MiPressCz\Core\Policies\GlobalSetPolicy;
use MiPressCz\Core\Policies\MediaFolderPolicy;
use MiPressCz\Core\Policies\MediaTagPolicy;
use MiPressCz\Core\Policies\TaxonomyPolicy;
use MiPressCz\Core\Policies\TermPolicy;
use MiPressCz\Core\Services\CacheService;
use MiPressCz\Core\Services\ComputedFieldRegistry;
use MiPressCz\Core\Services\LocaleService;
use MiPressCz\Core\Services\TemplateManager;
use MiPressCz\Core\Support\Blink;
use MiPressCz\Core\View\Composers\NavComposer;

class MiPressCzCoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Blink::class);
        $this->app->singleton(CacheService::class);
        $this->app->singleton(ComputedFieldRegistry::class);
        $this->app->singleton(LocaleService::class);
        $this->app->singleton(TemplateManager::class);

        // Register core lang files as additional base-namespace paths so that
        // __('content.*') and __('locales.*') resolve from the package when not
        // overridden by the host application's own lang directory.
        // IMPORTANT: must be in register() — boot() is too late because Filament
        // panel providers (listed in bootstrap/providers.php) boot before
        // auto-discovered providers and may call __() during their boot phase.
        $this->callAfterResolving('translation.loader', function ($loader): void {
            $loader->addPath(__DIR__.'/../resources/lang');
        });
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'mipresscz-core');

        // Register the 'template' Blade namespace pointing to the active template
        // with automatic fallback to the 'default' template directory.
        $this->app->make(TemplateManager::class)->registerViewNamespace();

        // Nav view composer — provides cached nav entries to header/footer partials.
        $this->app->make('view')->composer(
            ['template::partials.header', 'template::partials.footer'],
            NavComposer::class,
        );

        Factory::guessFactoryNamesUsing(function (string $modelName): string {
            if (str_starts_with($modelName, 'MiPressCz\\Core\\Models\\')) {
                return 'MiPressCz\\Core\\Database\\Factories\\'.class_basename($modelName).'Factory';
            }

            return 'Database\\Factories\\'.class_basename($modelName).'Factory';
        });

        Gate::policy(Blueprint::class, BlueprintPolicy::class);
        Gate::policy(Collection::class, CollectionPolicy::class);
        Gate::policy(Entry::class, EntryPolicy::class);
        Gate::policy(GlobalSet::class, GlobalSetPolicy::class);
        Gate::policy(MediaFolder::class, MediaFolderPolicy::class);
        Gate::policy(MediaTag::class, MediaTagPolicy::class);
        Gate::policy(Taxonomy::class, TaxonomyPolicy::class);
        Gate::policy(Term::class, TermPolicy::class);

        Entry::observe(EntryObserver::class);
        Locale::observe(LocaleObserver::class);

        $this->registerDefaultComputedFields();

        Event::subscribe(CacheInvalidationSubscriber::class);

        // Core catch-all frontend routes are registered after the application's
        // own routes so that app-specific routes always take precedence.
        // $this->app->booted() fires after withRouting() loads web routes.
        $this->app->booted(function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                TemplateListCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'mipresscz-migrations');

            $this->publishes([
                __DIR__.'/../resources/lang' => lang_path(),
            ], 'mipresscz-translations');
        }
    }

    /**
     * Register built-in computed fields (word_count, reading_time) for all collections.
     */
    private function registerDefaultComputedFields(): void
    {
        $registry = $this->app->make(ComputedFieldRegistry::class);

        $registry->register(ComputedFieldRegistry::WILDCARD, [
            'word_count' => fn (Entry $entry): int => str_word_count($entry->getPlainTextContent()),
            'reading_time' => fn (Entry $entry): int => max(1, (int) ceil(str_word_count($entry->getPlainTextContent()) / 200)),
        ]);
    }
}

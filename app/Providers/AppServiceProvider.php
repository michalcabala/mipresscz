<?php

namespace App\Providers;

use App\Mason\BrickCollection;
use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;
use App\Models\GlobalSet;
use App\Models\Locale;
use App\Models\Taxonomy;
use BezhanSalleh\LanguageSwitch\Enums\Placement;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use MiPressCz\Core\Filament\Resources\Entries\Schemas\EntryForm;
use MiPressCz\Core\Http\Controllers\EntryController;
use MiPressCz\Core\Observers\EntryObserver;
use MiPressCz\Core\Observers\LocaleObserver;
use MiPressCz\Core\Policies\BlueprintPolicy;
use MiPressCz\Core\Policies\CollectionPolicy;
use MiPressCz\Core\Policies\EntryPolicy;
use MiPressCz\Core\Policies\GlobalSetPolicy;
use MiPressCz\Core\Policies\TaxonomyPolicy;
use MiPressCz\Core\Services\LocaleService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // LocaleService is registered as a singleton in MiPressCzCoreServiceProvider.
    }

    public function boot(): void
    {
        EntryForm::$brickClasses = BrickCollection::all();
        EntryController::$brickClasses = BrickCollection::all();

        Gate::before(function ($user, $ability) {
            return $user->isSuperAdmin() ? true : null;
        });

        // Map app model wrappers to core policies so that both the core model
        // and the thin app wrapper benefit from the same policy logic.
        Gate::policy(Blueprint::class, BlueprintPolicy::class);
        Gate::policy(Collection::class, CollectionPolicy::class);
        Gate::policy(Entry::class, EntryPolicy::class);
        Gate::policy(GlobalSet::class, GlobalSetPolicy::class);
        Gate::policy(Taxonomy::class, TaxonomyPolicy::class);

        Entry::observe(EntryObserver::class);
        Locale::observe(LocaleObserver::class);

        Table::configureUsing(function (Table $table): void {
            $table
                ->striped()
                ->deferLoading()
                ->stackedOnMobile();
        });

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $config = locales()->toLanguageSwitchConfig();

            $switch
                ->locales($config['locales'])
                ->labels($config['labels'])
                ->flags($config['flags'])
                ->circular()
                ->visible(outsidePanels: true)
                ->outsidePanelPlacement(Placement::TopRight);
        });
    }
}

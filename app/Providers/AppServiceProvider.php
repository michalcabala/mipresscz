<?php

namespace App\Providers;

use App\Mason\BrickCollection;
use App\Models\Entry;
use App\Models\Locale;
use BezhanSalleh\LanguageSwitch\Enums\Placement;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use MiPressCz\Core\Filament\Resources\Entries\Schemas\EntryForm;
use MiPressCz\Core\Observers\EntryObserver;
use MiPressCz\Core\Observers\LocaleObserver;
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

        Gate::before(function ($user, $ability) {
            return $user->isSuperAdmin() ? true : null;
        });

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

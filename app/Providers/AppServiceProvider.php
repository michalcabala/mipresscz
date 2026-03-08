<?php

namespace App\Providers;

use App\Models\Entry;
use App\Models\Locale;
use App\Observers\EntryObserver;
use App\Observers\LocaleObserver;
use App\Services\LocaleService;
use BezhanSalleh\LanguageSwitch\Enums\Placement;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LocaleService::class);
    }

    public function boot(): void
    {
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

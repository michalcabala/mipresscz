<?php

namespace App\Providers;

use App\Models\Entry;
use App\Observers\EntryObserver;
use BezhanSalleh\LanguageSwitch\Enums\Placement;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->isSuperAdmin() ? true : null;
        });

        Entry::observe(EntryObserver::class);

        Table::configureUsing(function (Table $table): void {
            $table
                ->striped()
                ->deferLoading()
                ->stackedOnMobile();
        });

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['cs', 'en'])
                ->labels([
                    'cs' => 'Čeština',
                    'en' => 'English',
                ])
                ->flags([
                    'cs' => asset('assets/flags/CZ.svg'),
                    'en' => asset('assets/flags/GB-UKM.svg'),
                ])
                ->circular()
                ->visible(outsidePanels: true)
                ->outsidePanelPlacement(Placement::TopRight);
        });
    }
}

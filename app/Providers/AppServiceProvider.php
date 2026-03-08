<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use BezhanSalleh\LanguageSwitch\Enums\Placement;

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

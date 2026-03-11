<?php

namespace App\Providers;

use App\Mason\BrickCollection;
use BezhanSalleh\LanguageSwitch\Enums\Placement;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use MiPressCz\Core\Filament\Resources\Entries\Schemas\EntryForm;
use MiPressCz\Core\Http\Controllers\EntryController;

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

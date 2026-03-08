<?php

namespace App\Providers\Filament;

use App\Filament\Resources\Entries\EntryResource;
use App\Models\Collection;
use Awcodes\Curator\CuratorPlugin;
use Caresome\FilamentAuthDesigner\AuthDesignerPlugin;
use Caresome\FilamentAuthDesigner\Data\AuthPageConfig;
use Caresome\FilamentAuthDesigner\Enums\MediaPosition;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('mpcp')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->passwordReset()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->brandLogo(asset('assets/images/mipress-logo.svg'))
            ->darkModeBrandLogo(asset('assets/images/mipress-logo-white.svg'))
            ->favicon(asset('assets/images/favicon.svg'))
            ->maxContentWidth(Width::Full)
            ->spa()
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(fn (): string => __('content.entries.navigation_group')),
                NavigationGroup::make()
                    ->label(fn (): string => __('content.collections.navigation_group')),
                NavigationGroup::make()
                    ->label(fn (): string => __('users.navigation_group')),
                NavigationGroup::make()
                    ->label(fn (): string => __('locales.navigation_group'))
                    ->collapsed(),
            ])
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_AFTER,
                fn (): string => Blade::render('<x-filament::icon-button
                    icon="heroicon-o-arrow-top-right-on-square"
                    :href="url(\'/\')"
                    tag="a"
                    target="_blank"
                    :tooltip="__(\'panel.view_site\')"
                    :label="__(\'panel.view_site\')"
                    color="gray"
                />'),
            )
            ->unsavedChangesAlerts()
            ->databaseTransactions()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->resources($this->getCollectionResources())
            ->plugins([
                CuratorPlugin::make()
                    ->label('Médium')
                    ->pluralLabel('Média')
                    ->navigationGroup(__('content.entries.navigation_group'))
                    ->navigationSort(99),
                AuthDesignerPlugin::make()
                    ->login(
                        fn (AuthPageConfig $config) => $config
                            ->media(asset('assets/auth-bg.webp'))
                            ->mediaPosition(MediaPosition::Left)
                            ->blur(8)
                            ->themeToggle(top: '1rem', left: '1rem')
                    ),
                BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true,
                        userMenuLabel: 'My Profile',
                        shouldRegisterNavigation: false,
                        navigationGroup: 'Settings',
                        hasAvatars: false,
                        slug: 'my-profile'
                    )
                    ->enableBrowserSessions(condition: true),
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    /**
     * @return array<int, \Filament\Resources\ResourceConfiguration>
     */
    protected function getCollectionResources(): array
    {
        if (! Schema::hasTable('collections')) {
            return [];
        }

        return Collection::query()
            ->where('is_active', true)
            ->orderBy('title')
            ->get()
            ->map(fn (Collection $collection, int $index) => EntryResource::make($collection->handle)
                ->slug($collection->handle)
                ->collectionHandle($collection->handle)
                ->navigationLabel($collection->title)
                ->navigationIcon($collection->icon ?? 'fal-file-lines')
                ->navigationSort($index + 1)
            )
            ->all();
    }
}

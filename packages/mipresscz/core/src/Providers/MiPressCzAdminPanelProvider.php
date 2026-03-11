<?php

namespace MiPressCz\Core\Providers;

use Awcodes\Curator\CuratorPlugin;
use Caresome\FilamentAuthDesigner\AuthDesignerPlugin;
use Caresome\FilamentAuthDesigner\Data\AuthPageConfig;
use Caresome\FilamentAuthDesigner\Enums\MediaPosition;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
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
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use MiPressCz\Core\Filament\Widgets\EntryStatsWidget;
use MiPressCz\Core\Filament\Widgets\LatestEntriesWidget;

/**
 * Pre-configured Filament admin panel for miPressCZ.
 *
 * Extend this class in the application and override only the hook methods
 * you need to customise. Each hook configures exactly one concern area.
 *
 * @example Minimal usage (all defaults):
 *     class AdminPanelProvider extends MiPressCzAdminPanelProvider {}
 * @example Custom path/color:
 *     class AdminPanelProvider extends MiPressCzAdminPanelProvider
 *     {
 *         protected function configureBase(Panel $panel): Panel
 *         {
 *             return parent::configureBase($panel)->path('admin');
 *         }
 *     }
 */
class MiPressCzAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel = $this->configureBase($panel);
        $panel = $this->configureNavigation($panel);
        $panel = $this->configurePlugins($panel);
        $panel = $this->configureMiddleware($panel);
        $panel = $this->configureDiscovery($panel);

        return $panel;
    }

    /**
     * Base panel identity, appearance, and behaviour.
     *
     * Override to change path, colors, branding, SPA mode, etc.
     */
    protected function configureBase(Panel $panel): Panel
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
            ->sidebarCollapsibleOnDesktop()
            ->spa()
            ->unsavedChangesAlerts()
            ->databaseTransactions();
    }

    /**
     * Navigation groups, sidebar render hooks, and dynamic nav items.
     *
     * Override to reorder or rename groups, or inject additional navigation items.
     */
    protected function configureNavigation(Panel $panel): Panel
    {
        return $panel
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(fn (): string => __('content.entries.navigation_group')),
                NavigationGroup::make()
                    ->label(fn (): string => __('content.collections.navigation_group')),
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
            ->navigationItems($this->getTaxonomyNavigationItems());
    }

    /**
     * Filament plugins: Curator media, AuthDesigner, Breezy profile.
     *
     * Override to swap or reconfigure plugins.
     */
    protected function configurePlugins(Panel $panel): Panel
    {
        return $panel->plugins([
            CuratorPlugin::make()
                ->label(__('content.media.label'))
                ->pluralLabel(__('content.media.plural_label'))
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
                    userMenuLabel: __('panel.my_profile'),
                    shouldRegisterNavigation: false,
                    navigationGroup: 'Settings',
                    hasAvatars: false,
                    slug: 'my-profile'
                )
                ->enableBrowserSessions(condition: true),
        ]);
    }

    /**
     * HTTP and auth middleware stack.
     *
     * Override to add custom middleware (e.g. SetFrontendLocale, tenant resolvers).
     */
    protected function configureMiddleware(Panel $panel): Panel
    {
        return $panel
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
     * Resource, page, and widget auto-discovery.
     *
     * Override to add extra discovery paths or remove defaults.
     */
    protected function configureDiscovery(Panel $panel): Panel
    {
        return $panel
            ->discoverResources(in: __DIR__.'/../Filament/Resources', for: 'MiPressCz\Core\Filament\Resources')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: __DIR__.'/../Filament/Pages', for: 'MiPressCz\Core\Filament\Pages')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->resources($this->getCollectionResources())
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
                EntryStatsWidget::class,
                LatestEntriesWidget::class,
            ]);
    }

    /**
     * Returns dynamically registered Filament resources for active collections.
     *
     * Override in the application to inject per-collection EntryResource instances.
     *
     * @return array<int, mixed>
     */
    protected function getCollectionResources(): array
    {
        return [];
    }

    /**
     * Returns navigation items for taxonomy term lists, grouped under their collections.
     *
     * Override in the application to inject taxonomy navigation items.
     *
     * @return array<int, NavigationItem>
     */
    protected function getTaxonomyNavigationItems(): array
    {
        return [];
    }
}

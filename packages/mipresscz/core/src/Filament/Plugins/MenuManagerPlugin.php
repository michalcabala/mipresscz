<?php

namespace MiPressCz\Core\Filament\Plugins;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Icons\Heroicon;
use MiPressCz\Core\Filament\Pages\MenuManagerPage;
use MiPressCz\Core\Services\NavMenuService;

class MenuManagerPlugin implements Plugin
{
    /** @var array<string, string> */
    protected array $locations = [];

    /** @var array<int, class-string> */
    protected array $modelSources = [];

    protected ?string $navigationLabel = null;

    protected ?string $navigationGroup = null;

    protected Heroicon|string|null $navigationIcon = null;

    protected ?int $navigationSort = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function getId(): string
    {
        return 'nav-menu-manager';
    }

    // -------------------------------------------------------------------------
    // Fluent configuration methods
    // -------------------------------------------------------------------------

    /**
     * @param  array<string, string>  $locations
     */
    public function locations(array $locations): static
    {
        $this->locations = $locations;

        return $this;
    }

    /**
     * @param  array<int, class-string>  $sources
     */
    public function modelSources(array $sources): static
    {
        $this->modelSources = $sources;

        return $this;
    }

    public function navigationLabel(string $label): static
    {
        $this->navigationLabel = $label;

        return $this;
    }

    public function navigationGroup(string $group): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function navigationIcon(Heroicon|string $icon): static
    {
        $this->navigationIcon = $icon;

        return $this;
    }

    public function navigationSort(int $sort): static
    {
        $this->navigationSort = $sort;

        return $this;
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function getNavigationLabel(): ?string
    {
        return $this->navigationLabel;
    }

    public function getNavigationGroup(): ?string
    {
        return $this->navigationGroup;
    }

    public function getNavigationIcon(): Heroicon|string|null
    {
        return $this->navigationIcon;
    }

    public function getNavigationSort(): ?int
    {
        return $this->navigationSort;
    }

    // -------------------------------------------------------------------------
    // Plugin lifecycle
    // -------------------------------------------------------------------------

    public function register(Panel $panel): void
    {
        $panel->pages([MenuManagerPage::class]);
    }

    public function boot(Panel $panel): void
    {
        $service = app(NavMenuService::class);
        $service->registerLocations($this->locations);
        $service->registerModelSources($this->modelSources);
        $service->syncLocations();
    }
}

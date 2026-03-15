<?php

namespace MiPressCz\Core\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Illuminate\Support\Collection;
use MiPressCz\Core\Filament\Plugins\MenuManagerPlugin;
use MiPressCz\Core\Models\NavMenu;
use MiPressCz\Core\Services\NavMenuService;

class MenuManagerPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'mipresscz-core::filament.pages.nav-menu-manager';

    // -------------------------------------------------------------------------
    // Navigation — pulled from plugin configuration
    // -------------------------------------------------------------------------

    public static function getNavigationGroup(): \UnitEnum|string|null
    {
        return MenuManagerPlugin::get()->getNavigationGroup() ?? static::$navigationGroup ?? __('Settings');
    }

    public static function getNavigationIcon(): \BackedEnum|string|null
    {
        return MenuManagerPlugin::get()->getNavigationIcon() ?? 'heroicon-o-bars-3';
    }

    public static function getNavigationLabel(): string
    {
        return (string) (MenuManagerPlugin::get()->getNavigationLabel() ?? __('content.menus.plural_label'));
    }

    public static function getNavigationSort(): ?int
    {
        return MenuManagerPlugin::get()->getNavigationSort() ?? 99;
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'menu-manager';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('manage.menus') ?? false;
    }

    // -------------------------------------------------------------------------
    // State
    // -------------------------------------------------------------------------

    public ?string $activeLocation = null;

    public ?int $activeMenuId = null;

    // -------------------------------------------------------------------------
    // Lifecycle
    // -------------------------------------------------------------------------

    public function mount(): void
    {
        $locations = $this->getLocations();

        if (! empty($locations)) {
            $firstHandle = array_key_first($locations);
            $this->activeLocation = $firstHandle;

            $menus = $this->getMenusForActiveLocation();
            if ($menus->isNotEmpty()) {
                $this->activeMenuId = $menus->first()->id;
            }
        }
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /** @return array<string, string> */
    public function getLocations(): array
    {
        return app(NavMenuService::class)->allLocations();
    }

    /** @return Collection<int, NavMenu> */
    public function getMenusForActiveLocation(): Collection
    {
        if (! $this->activeLocation) {
            return collect();
        }

        return NavMenu::query()
            ->whereHas('location', fn ($q) => $q->where('handle', $this->activeLocation))
            ->orderBy('name')
            ->get();
    }

    // -------------------------------------------------------------------------
    // Actions
    // -------------------------------------------------------------------------

    public function switchLocation(string $handle): void
    {
        $this->activeLocation = $handle;
        $this->activeMenuId = null;

        $menus = $this->getMenusForActiveLocation();
        if ($menus->isNotEmpty()) {
            $this->activeMenuId = $menus->first()->id;
        }

        $this->dispatch('menuIdChanged', menuId: $this->activeMenuId ?? 0);
    }

    public function switchMenu(int $menuId): void
    {
        $this->activeMenuId = $menuId;
        $this->dispatch('menuIdChanged', menuId: $menuId);
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        return [
            'locations' => $this->getLocations(),
            'menusForActiveLocation' => $this->getMenusForActiveLocation(),
        ];
    }

    /** @return array<int, Action> */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('createMenu')
                ->label(__('content.menus.create_menu'))
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->form([
                    Select::make('menu_location_id')
                        ->label(__('content.menus.location'))
                        ->options(fn (): array => \MiPressCz\Core\Models\NavMenuLocation::query()
                            ->whereIn('handle', array_keys($this->getLocations()))
                            ->pluck('name', 'id')
                            ->toArray())
                        ->required(),
                    TextInput::make('name')
                        ->label(__('content.menus.name'))
                        ->required()
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    $menu = NavMenu::query()->create($data);
                    $this->activeMenuId = $menu->id;
                    $this->dispatch('menuIdChanged', menuId: $menu->id);
                    Notification::make('menu_created')
                        ->title(__('content.menus.created_notification'))
                        ->success()
                        ->send();
                }),

            Action::make('deleteMenu')
                ->label(__('content.menus.delete_menu'))
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->activeMenuId !== null)
                ->action(function (): void {
                    if ($this->activeMenuId) {
                        NavMenu::query()->destroy($this->activeMenuId);
                        $this->activeMenuId = null;

                        $menus = $this->getMenusForActiveLocation();
                        if ($menus->isNotEmpty()) {
                            $this->activeMenuId = $menus->first()->id;
                        }

                        $this->dispatch('menuIdChanged', menuId: $this->activeMenuId ?? 0);

                        Notification::make('menu_deleted')
                            ->title(__('content.menus.deleted_notification'))
                            ->success()
                            ->send();
                    }
                }),
        ];
    }
}

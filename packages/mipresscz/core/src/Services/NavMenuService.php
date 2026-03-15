<?php

namespace MiPressCz\Core\Services;

use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\NavMenu;
use MiPressCz\Core\Models\NavMenuItem;
use MiPressCz\Core\Models\NavMenuLocation;

class NavMenuService
{
    /** @var array<string, string> */
    protected array $locations = [];

    /** @var array<int, class-string> */
    protected array $modelSources = [];

    /**
     * Register available menu locations (handle => display name).
     *
     * @param  array<string, string>  $locations
     */
    public function registerLocations(array $locations): static
    {
        $this->locations = $locations;

        return $this;
    }

    /**
     * Register Eloquent model classes that can be added as menu items.
     *
     * @param  array<int, class-string>  $sources
     */
    public function registerModelSources(array $sources): static
    {
        $this->modelSources = $sources;

        return $this;
    }

    /**
     * Returns all registered location definitions.
     *
     * @return array<string, string>
     */
    public function allLocations(): array
    {
        return $this->locations;
    }

    /**
     * Returns all registered model source class names.
     *
     * @return array<int, class-string>
     */
    public function getModelSources(): array
    {
        return $this->modelSources;
    }

    /**
     * Returns collection archive sources that can be added as standalone menu items.
     *
     * @return array<int, array{handle: string, title: string, url: string, icon: string|null}>
     */
    public function getArchiveSources(): array
    {
        return Collection::query()
            ->where('is_active', true)
            ->whereNotNull('route_template')
            ->orderBy('title')
            ->get()
            ->map(function (Collection $collection): ?array {
                $archivePath = $this->resolveArchivePath($collection->route_template);

                if ($archivePath === null) {
                    return null;
                }

                return [
                    'handle' => $collection->handle,
                    'title' => $collection->getLocalizedTitle(),
                    'url' => url($archivePath),
                    'icon' => $collection->icon,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Synchronise the `fmm_menu_locations` table to match the registered locations.
     */
    public function syncLocations(): void
    {
        foreach ($this->locations as $handle => $name) {
            NavMenuLocation::updateOrCreate(
                ['handle' => $handle],
                ['name' => $name],
            );
        }
    }

    /**
     * Recursively persist a nested tree array back to the database.
     *
     * @param  array<int, array<string, mixed>>  $tree
     */
    public function saveTree(int $menuId, array $tree, ?int $parentId, int &$order): void
    {
        foreach ($tree as $node) {
            $item = NavMenuItem::query()->find($node['id']);

            if (! $item) {
                continue;
            }

            $item->update([
                'parent_id' => $parentId,
                'order' => $order++,
            ]);

            if (! empty($node['children'])) {
                $this->saveTree($menuId, $node['children'], $item->id, $order);
            }
        }
    }

    /**
     * Return the nested item tree for the first active menu at a given location.
     *
     * Each node contains: id, title, url, target, icon, enabled, type, children[].
     *
     * @return array<int, array<string, mixed>>
     */
    public function getMenuTree(string $locationHandle): array
    {
        $location = NavMenuLocation::query()
            ->where('handle', $locationHandle)
            ->first();

        if (! $location) {
            return [];
        }

        $menu = NavMenu::query()
            ->where('menu_location_id', $location->id)
            ->where('is_active', true)
            ->first();

        if (! $menu) {
            return [];
        }

        return $this->buildFrontendTree($menu);
    }

    /**
     * Build a frontend-ready nested tree from a NavMenu.
     *
     * Only enabled items are included. Each node is a plain array with
     * resolved title, url, target, icon, type and children.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function buildFrontendTree(NavMenu $menu): array
    {
        $items = $menu->items()
            ->where('enabled', true)
            ->orderBy('order')
            ->get();

        $grouped = $items->groupBy(fn (NavMenuItem $i) => $i->parent_id ?? 0);

        $build = function (int|string $parentId) use ($grouped, &$build): array {
            return $grouped->get($parentId, collect())
                ->map(function (NavMenuItem $item) use ($build): array {
                    return [
                        'id' => $item->id,
                        'title' => $item->getResolvedTitle(),
                        'url' => $item->getResolvedUrl(),
                        'target' => $item->target ?? '_self',
                        'icon' => $item->icon,
                        'type' => $item->type ?? 'custom',
                        'children' => $build($item->id),
                    ];
                })
                ->values()
                ->all();
        };

        return $build(0);
    }

    protected function resolveArchivePath(?string $routeTemplate): ?string
    {
        if (blank($routeTemplate) || ! str_contains($routeTemplate, '{slug')) {
            return null;
        }

        $path = preg_replace('/\/?\{slug[^}]*\}.*$/', '', trim($routeTemplate));
        $path = '/'.ltrim((string) $path, '/');
        $path = rtrim($path, '/');

        if ($path === '') {
            $path = '/';
        }

        return $path === '/' ? null : $path;
    }
}

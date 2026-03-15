<?php

namespace MiPressCz\Core\Services;

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
}

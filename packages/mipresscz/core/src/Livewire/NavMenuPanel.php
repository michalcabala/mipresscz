<?php

namespace MiPressCz\Core\Livewire;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use MiPressCz\Core\Models\NavMenuItem;
use MiPressCz\Core\Services\NavMenuService;

class NavMenuPanel extends Component
{
    /** @var array<string, array<int, string>> */
    protected static array $searchableColumnsCache = [];

    // -------------------------------------------------------------------------
    // Props
    // -------------------------------------------------------------------------

    public ?int $menuId = null;

    public string $locationHandle = '';

    // -------------------------------------------------------------------------
    // State
    // -------------------------------------------------------------------------

    public string $activeTab = 'custom';

    public string $customTitle = '';

    public string $customUrl = '';

    public string $customTarget = '_self';

    public string $modelSearch = '';

    /** @var array<string, array<int, int|string>> */
    public array $usedModels = [];

    /** @var array<int, string> */
    public array $usedArchives = [];

    // -------------------------------------------------------------------------
    // Listeners
    // -------------------------------------------------------------------------

    /** @return array<string, string> */
    protected function getListeners(): array
    {
        return [
            'menuIdChanged' => 'onMenuChanged',
            'menu-content-updated' => 'refreshUsedItems',
        ];
    }

    public function onMenuChanged(int $menuId): void
    {
        $this->menuId = $menuId;
        $this->refreshUsedItems();
    }

    public function mount(): void
    {
        if ($this->menuId) {
            $this->refreshUsedItems();
        }
    }

    public function refreshUsedItems(): void
    {
        if (! $this->menuId) {
            $this->usedModels = [];
            $this->usedArchives = [];

            return;
        }

        $items = NavMenuItem::query()
            ->where('menu_id', $this->menuId)
            ->get();

        $this->usedModels = $items
            ->map(function (NavMenuItem $item): ?array {
                $linkableType = $item->linkable_type ?? data_get($item->data, 'linkable_type');
                $linkableId = $item->linkable_id ?? data_get($item->data, 'linkable_id');

                if (blank($linkableType) || blank($linkableId)) {
                    return null;
                }

                return [
                    'type' => (string) $linkableType,
                    'id' => $linkableId,
                ];
            })
            ->filter()
            ->groupBy('type')
            ->map(fn (Collection $items): array => $items->pluck('id')->all())
            ->toArray();

        $this->usedArchives = $items
            ->where('type', 'archive')
            ->map(fn (NavMenuItem $item): ?string => data_get($item->data, 'archive_handle'))
            ->filter()
            ->values()
            ->all();
    }

    // -------------------------------------------------------------------------
    // Actions
    // -------------------------------------------------------------------------

    public function addCustomLink(): void
    {
        if (blank($this->customTitle)) {
            return;
        }

        $this->createMenuItem([
            'title' => $this->customTitle,
            'url' => $this->customUrl ?: '#',
            'target' => $this->customTarget,
            'type' => 'custom',
        ]);

        $this->customTitle = '';
        $this->customUrl = '';
        $this->customTarget = '_self';
    }

    public function addModelItem(string $modelClass, string|int $modelId): void
    {
        if (! class_exists($modelClass)) {
            return;
        }

        $model = $modelClass::query()->find($modelId);

        if (! $model) {
            return;
        }

        if (in_array($modelId, $this->usedModels[$modelClass] ?? [])) {
            return;
        }

        $attributes = [
            'title' => $model->getMenuLabel(),
            'url' => $model->getMenuUrl(),
            'target' => $model->getMenuTarget(),
            'icon' => $model->getMenuIcon(),
            'type' => 'model',
        ];

        if (is_int($modelId) || ctype_digit((string) $modelId)) {
            $attributes['linkable_type'] = $modelClass;
            $attributes['linkable_id'] = (int) $modelId;
        } else {
            $attributes['data'] = [
                'linkable_type' => $modelClass,
                'linkable_id' => (string) $modelId,
            ];
        }

        $this->createMenuItem($attributes);
    }

    public function addArchiveItem(string $archiveHandle): void
    {
        $archive = collect($this->getArchiveSources())
            ->firstWhere('handle', $archiveHandle);

        if (! $archive || in_array($archiveHandle, $this->usedArchives, true)) {
            return;
        }

        $this->createMenuItem([
            'title' => $archive['title'],
            'url' => $archive['url'],
            'target' => '_self',
            'icon' => $archive['icon'],
            'type' => 'archive',
            'data' => [
                'archive_handle' => $archive['handle'],
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Computed helpers
    // -------------------------------------------------------------------------

    /** @return array<int, class-string> */
    public function getModelSources(): array
    {
        return app(NavMenuService::class)->getModelSources();
    }

    /**
     * @return array<int, array{handle: string, title: string, url: string, icon: string|null}>
     */
    public function getArchiveSources(): array
    {
        return app(NavMenuService::class)->getArchiveSources();
    }

    public function getModelRecords(string $modelClass): Collection
    {
        if (! class_exists($modelClass)) {
            return collect();
        }

        $query = $modelClass::query();

        if ($this->modelSearch) {
            $columns = $this->getSearchableColumns($modelClass);
            $search = $this->modelSearch;

            $query->where(function ($q) use ($columns, $search): void {
                foreach (['name', 'title', 'label'] as $col) {
                    if (in_array($col, $columns)) {
                        $q->orWhere($col, 'like', "%{$search}%");
                    }
                }
            });
        }

        return $query->limit(50)->get();
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    public function render(): \Illuminate\View\View
    {
        return view('mipresscz-core::livewire.nav-menu.panel', [
            'modelSources' => $this->activeTab === 'models' ? $this->getModelSources() : [],
            'archiveSources' => $this->activeTab === 'archives' ? $this->getArchiveSources() : [],
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function createMenuItem(array $data): void
    {
        if (! $this->menuId) {
            return;
        }

        $maxOrder = NavMenuItem::query()
            ->where('menu_id', $this->menuId)
            ->whereNull('parent_id')
            ->max('order') ?? 0;

        NavMenuItem::query()->create(array_merge([
            'menu_id' => $this->menuId,
            'parent_id' => null,
            'order' => $maxOrder + 1,
            'target' => '_self',
            'enabled' => true,
            'type' => 'custom',
        ], $data));

        $this->refreshUsedItems();
        $this->dispatch('menu-content-updated')->to(NavMenuBuilder::class);
        $this->dispatch('menu-saved');
    }

    /**
     * @return array<int, string>
     */
    protected function getSearchableColumns(string $modelClass): array
    {
        if (isset(static::$searchableColumnsCache[$modelClass])) {
            return static::$searchableColumnsCache[$modelClass];
        }

        $table = (new $modelClass)->getTable();

        return static::$searchableColumnsCache[$modelClass] = Schema::getColumnListing($table);
    }
}

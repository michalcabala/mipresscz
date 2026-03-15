<?php

namespace MiPressCz\Core\Livewire;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use MiPressCz\Core\Models\NavMenuItem;
use MiPressCz\Core\Services\NavMenuService;

class NavMenuPanel extends Component
{
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

    // -------------------------------------------------------------------------
    // Listeners
    // -------------------------------------------------------------------------

    /** @return array<string, string> */
    protected function getListeners(): array
    {
        return [
            'menuIdChanged' => 'onMenuChanged',
            'menu-content-updated' => 'refreshUsedModels',
        ];
    }

    public function onMenuChanged(int $menuId): void
    {
        $this->menuId = $menuId;
        $this->refreshUsedModels();
    }

    public function mount(): void
    {
        if ($this->menuId) {
            $this->refreshUsedModels();
        }
    }

    public function refreshUsedModels(): void
    {
        if (! $this->menuId) {
            $this->usedModels = [];

            return;
        }

        $this->usedModels = NavMenuItem::query()
            ->where('menu_id', $this->menuId)
            ->whereNotNull('linkable_type')
            ->whereNotNull('linkable_id')
            ->get()
            ->groupBy('linkable_type')
            ->map(fn (Collection $items): array => $items->pluck('linkable_id')->all())
            ->toArray();
    }

    // -------------------------------------------------------------------------
    // Actions
    // -------------------------------------------------------------------------

    public function addCustomLink(): void
    {
        if (empty($this->customTitle)) {
            return;
        }

        $this->dispatch('menuItemAdded', [
            'title' => $this->customTitle,
            'url' => $this->customUrl ?: '#',
            'target' => $this->customTarget,
            'type' => 'custom',
        ]);

        $this->customTitle = '';
        $this->customUrl = '';
        $this->customTarget = '_self';
    }

    public function addModelItem(string $modelClass, int $modelId): void
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

        $this->dispatch('menuItemAdded', [
            'title' => $model->getMenuLabel(),
            'url' => $model->getMenuUrl(),
            'target' => $model->getMenuTarget(),
            'icon' => $model->getMenuIcon(),
            'type' => 'model',
            'linkable_type' => $modelClass,
            'linkable_id' => $modelId,
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

    public function getModelRecords(string $modelClass): Collection
    {
        if (! class_exists($modelClass)) {
            return collect();
        }

        $query = $modelClass::query();

        if ($this->modelSearch) {
            $table = (new $modelClass)->getTable();
            $columns = Schema::getColumnListing($table);
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
            'modelSources' => $this->getModelSources(),
        ]);
    }
}

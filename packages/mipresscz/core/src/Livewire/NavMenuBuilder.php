<?php

namespace MiPressCz\Core\Livewire;

use Livewire\Component;
use MiPressCz\Core\Models\NavMenu;
use MiPressCz\Core\Models\NavMenuItem;
use MiPressCz\Core\Services\NavMenuService;

class NavMenuBuilder extends Component
{
    // -------------------------------------------------------------------------
    // Props
    // -------------------------------------------------------------------------

    public ?int $menuId = null;

    public string $locationHandle = '';

    // -------------------------------------------------------------------------
    // State
    // -------------------------------------------------------------------------

    /** @var array<int, array<string, mixed>> */
    public array $items = [];

    public ?int $editingItemId = null;

    public string $editingTitle = '';

    public string $editingUrl = '';

    public string $editingTarget = '_self';

    public bool $editingEnabled = true;

    public bool $autoSave = true;

    public bool $isDirty = false;

    public ?int $maxDepth = null;

    public ?int $deletingItemId = null;

    // -------------------------------------------------------------------------
    // Lifecycle
    // -------------------------------------------------------------------------

    public function mount(?int $menuId = null, string $locationHandle = ''): void
    {
        $this->menuId = $menuId;
        $this->locationHandle = $locationHandle;
        $this->autoSave = config('nav-menu.auto_save', true);
        $this->maxDepth = config('nav-menu.max_depth');
        $this->loadItems();
    }

    public function updatedMenuId(): void
    {
        $this->loadItems();
        $this->editingItemId = null;
    }

    protected function loadItems(): void
    {
        if (! $this->menuId) {
            $this->items = [];

            return;
        }

        $menu = NavMenu::query()->find($this->menuId);
        $this->items = $menu ? $menu->getTree() : [];
    }

    // -------------------------------------------------------------------------
    // Listeners
    // -------------------------------------------------------------------------

    /** @return array<string, string> */
    protected function getListeners(): array
    {
        return [
            'menuItemAdded' => 'addItem',
            'menuIdChanged' => 'changeMenu',
        ];
    }

    public function changeMenu(int $menuId): void
    {
        $this->menuId = $menuId;
        $this->loadItems();
        $this->editingItemId = null;
    }

    // -------------------------------------------------------------------------
    // Drag & drop order
    // -------------------------------------------------------------------------

    /**
     * Called from JS after drag & drop — receives the new sorted nested tree.
     *
     * @param  array<int, array<string, mixed>>  $tree
     */
    public function updateOrder(array $tree): void
    {
        if (! $this->menuId) {
            return;
        }

        $order = 0;
        app(NavMenuService::class)->saveTree($this->menuId, $tree, null, $order);
        $this->loadItems();
        $this->isDirty = false;
        $this->dispatch('menu-saved');
    }

    // -------------------------------------------------------------------------
    // Button-based reorder
    // -------------------------------------------------------------------------

    public function moveUp(int $itemId): void
    {
        $this->shiftItem($itemId, 'up');
    }

    public function moveDown(int $itemId): void
    {
        $this->shiftItem($itemId, 'down');
    }

    public function indentItem(int $itemId): void
    {
        $item = NavMenuItem::query()->find($itemId);

        if (! $item) {
            return;
        }

        $sibling = NavMenuItem::query()
            ->where('menu_id', $item->menu_id)
            ->where('parent_id', $item->parent_id)
            ->where('order', '<', $item->order)
            ->orderByDesc('order')
            ->first();

        if (! $sibling) {
            return;
        }

        if ($this->maxDepth !== null) {
            $siblingDepth = $this->getItemDepth($sibling);
            $subtreeDepth = $this->getItemSubtreeDepth($item);

            if (($siblingDepth + 1 + $subtreeDepth) > $this->maxDepth) {
                return;
            }
        }

        $maxOrder = NavMenuItem::query()
            ->where('menu_id', $item->menu_id)
            ->where('parent_id', $sibling->id)
            ->max('order') ?? 0;

        $item->update(['parent_id' => $sibling->id, 'order' => $maxOrder + 1]);
        $this->loadItems();
    }

    public function outdentItem(int $itemId): void
    {
        $item = NavMenuItem::query()->find($itemId);

        if (! $item || ! $item->parent_id) {
            return;
        }

        $parent = $item->parent;
        $maxOrder = NavMenuItem::query()
            ->where('menu_id', $item->menu_id)
            ->where('parent_id', $parent?->parent_id)
            ->max('order') ?? 0;

        $item->update(['parent_id' => $parent?->parent_id, 'order' => $maxOrder + 1]);
        $this->loadItems();
    }

    protected function shiftItem(int $itemId, string $direction): void
    {
        $item = NavMenuItem::query()->find($itemId);

        if (! $item) {
            return;
        }

        $sibling = $direction === 'up'
            ? NavMenuItem::query()
                ->where('menu_id', $item->menu_id)
                ->where('parent_id', $item->parent_id)
                ->where('order', '<', $item->order)
                ->orderByDesc('order')
                ->first()
            : NavMenuItem::query()
                ->where('menu_id', $item->menu_id)
                ->where('parent_id', $item->parent_id)
                ->where('order', '>', $item->order)
                ->orderBy('order')
                ->first();

        if ($sibling) {
            [$item->order, $sibling->order] = [$sibling->order, $item->order];
            $item->save();
            $sibling->save();
            $this->loadItems();
        }
    }

    // -------------------------------------------------------------------------
    // Add / Remove / Edit
    // -------------------------------------------------------------------------

    /**
     * @param  array<string, mixed>  $data
     */
    public function addItem(array $data): void
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

        $this->loadItems();

        if ($this->autoSave) {
            $this->dispatch('menu-saved');
        }

        $this->dispatch('menu-content-updated');
    }

    public function confirmRemoval(int $itemId): void
    {
        $this->deletingItemId = $itemId;
    }

    public function cancelRemoval(): void
    {
        $this->deletingItemId = null;
    }

    public function removeItem(int $itemId): void
    {
        $item = NavMenuItem::query()->find($itemId);

        if ($item) {
            $this->deleteItemRecursive($item);
        }

        $this->loadItems();
        $this->dispatch('menu-content-updated');
        $this->deletingItemId = null;
    }

    protected function deleteItemRecursive(NavMenuItem $item): void
    {
        foreach ($item->children as $child) {
            $this->deleteItemRecursive($child);
        }

        $item->delete();
    }

    public function startEdit(int $itemId): void
    {
        $item = NavMenuItem::query()->find($itemId);

        if ($item) {
            $this->editingItemId = $itemId;
            $this->editingTitle = $item->title;
            $this->editingUrl = $item->url ?? '';
            $this->editingTarget = $item->target ?? '_self';
            $this->editingEnabled = (bool) $item->enabled;
        }
    }

    public function cancelEdit(): void
    {
        $this->editingItemId = null;
    }

    public function saveEdit(): void
    {
        if (! $this->editingItemId) {
            return;
        }

        NavMenuItem::query()->where('id', $this->editingItemId)->update([
            'title' => $this->editingTitle,
            'url' => $this->editingUrl,
            'target' => $this->editingTarget,
            'enabled' => $this->editingEnabled,
        ]);

        $this->editingItemId = null;
        $this->loadItems();

        if ($this->autoSave) {
            $this->dispatch('menu-saved');
        }
    }

    public function toggleEnabled(int $itemId): void
    {
        $item = NavMenuItem::query()->find($itemId);

        if ($item) {
            $item->update(['enabled' => ! $item->enabled]);
            $this->loadItems();
        }
    }

    // -------------------------------------------------------------------------
    // Depth helpers
    // -------------------------------------------------------------------------

    protected function getItemDepth(NavMenuItem $item, int $currentDepth = 0): int
    {
        if (! $item->parent_id) {
            return $currentDepth;
        }

        $parent = $item->parent;

        if (! $parent) {
            return $currentDepth;
        }

        return $this->getItemDepth($parent, $currentDepth + 1);
    }

    protected function getItemSubtreeDepth(NavMenuItem $item): int
    {
        $maxChildDepth = 0;

        foreach ($item->children as $child) {
            $childDepth = $this->getItemSubtreeDepth($child) + 1;
            if ($childDepth > $maxChildDepth) {
                $maxChildDepth = $childDepth;
            }
        }

        return $maxChildDepth;
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    public function render(): \Illuminate\View\View
    {
        return view('mipresscz-core::livewire.nav-menu.builder', [
            'items' => $this->items,
            'hasMenu' => $this->menuId !== null,
            'maxDepth' => $this->maxDepth,
        ]);
    }
}

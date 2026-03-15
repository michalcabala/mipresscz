<?php

namespace MiPressCz\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavMenu extends Model
{
    protected $table = 'fmm_menus';

    protected $fillable = ['menu_location_id', 'name', 'is_active'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    /** @return BelongsTo<NavMenuLocation, $this> */
    public function location(): BelongsTo
    {
        return $this->belongsTo(NavMenuLocation::class, 'menu_location_id');
    }

    /** @return HasMany<NavMenuItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(NavMenuItem::class, 'menu_id')->orderBy('order');
    }

    /** @return HasMany<NavMenuItem, $this> */
    public function rootItems(): HasMany
    {
        return $this->hasMany(NavMenuItem::class, 'menu_id')
            ->whereNull('parent_id')
            ->orderBy('order');
    }

    /**
     * Build a nested tree array from the flat items collection.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getTree(): array
    {
        $items = $this->items()->get()->keyBy('id');

        $build = function (NavMenuItem $item) use ($items, &$build): array {
            $children = $items
                ->filter(fn (NavMenuItem $i): bool => $i->parent_id === $item->id)
                ->sortBy('order')
                ->values();

            return array_merge($item->toArray(), [
                'children' => $children->map(fn (NavMenuItem $child) => $build($child))->values()->all(),
            ]);
        };

        return $items
            ->filter(fn (NavMenuItem $i): bool => is_null($i->parent_id))
            ->sortBy('order')
            ->map(fn (NavMenuItem $item) => $build($item))
            ->values()
            ->all();
    }
}

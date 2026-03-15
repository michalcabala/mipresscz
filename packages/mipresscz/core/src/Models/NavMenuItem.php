<?php

namespace MiPressCz\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class NavMenuItem extends Model
{
    protected $table = 'fmm_menu_items';

    protected $guarded = [];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'data' => 'array',
        ];
    }

    /** @return BelongsTo<NavMenu, $this> */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(NavMenu::class, 'menu_id');
    }

    /** @return BelongsTo<NavMenuItem, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(NavMenuItem::class, 'parent_id');
    }

    /** @return HasMany<NavMenuItem, $this> */
    public function children(): HasMany
    {
        return $this->hasMany(NavMenuItem::class, 'parent_id')->orderBy('order');
    }

    public function linkable(): MorphTo
    {
        return $this->morphTo('linkable');
    }

    /**
     * Returns the resolved URL, falling back to the linkable model's menu URL when set.
     */
    public function getResolvedUrl(): string
    {
        if ($this->url) {
            return $this->url;
        }

        if ($this->linkable && method_exists($this->linkable, 'getMenuUrl')) {
            return (string) $this->linkable->getMenuUrl();
        }

        return '#';
    }

    /**
     * Returns the resolved display title, falling back to the linkable model's menu label.
     */
    public function getResolvedTitle(): string
    {
        if ($this->title) {
            return $this->title;
        }

        if ($this->linkable && method_exists($this->linkable, 'getMenuLabel')) {
            return (string) $this->linkable->getMenuLabel();
        }

        return '';
    }
}

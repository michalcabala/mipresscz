<?php

namespace MiPressCz\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MiPressCz\Core\Enums\MenuItemTarget;
use MiPressCz\Core\Enums\MenuItemType;
use Openplain\FilamentTreeView\Concerns\HasTreeStructure;

class MenuItem extends Model
{
    use HasFactory, HasTreeStructure, HasUlids;

    protected $fillable = [
        'menu_id',
        'parent_id',
        'order',
        'type',
        'title',
        'url',
        'target',
        'entry_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => MenuItemType::class,
            'target' => MenuItemTarget::class,
            'is_active' => 'boolean',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(Entry::class);
    }
}

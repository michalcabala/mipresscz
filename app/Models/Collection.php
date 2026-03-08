<?php

namespace App\Models;

use App\Enums\DateBehavior;
use App\Enums\DefaultStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collection extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'name',
        'title',
        'handle',
        'description',
        'is_tree',
        'route_template',
        'sort_field',
        'sort_direction',
        'date_behavior',
        'revisions_enabled',
        'default_status',
        'icon',
        'is_active',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_tree' => 'boolean',
            'revisions_enabled' => 'boolean',
            'is_active' => 'boolean',
            'date_behavior' => DateBehavior::class,
            'default_status' => DefaultStatus::class,
            'settings' => 'array',
        ];
    }

    public function blueprints(): HasMany
    {
        return $this->hasMany(Blueprint::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class);
    }

    public function taxonomies(): BelongsToMany
    {
        return $this->belongsToMany(Taxonomy::class, 'collection_taxonomy');
    }

    public function defaultBlueprint(): ?Blueprint
    {
        return $this->blueprints()->where('is_default', true)->first();
    }
}

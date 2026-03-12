<?php

namespace MiPressCz\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use MiPressCz\Core\Models\Concerns\HasLocalizedTitle;

class Taxonomy extends Model
{
    use HasFactory, HasLocalizedTitle, HasUlids, SoftDeletes;

    protected $fillable = [
        'name',
        'title',
        'handle',
        'description',
        'is_hierarchical',
        'is_active',
        'settings',
        'translations',
    ];

    protected function casts(): array
    {
        return [
            'is_hierarchical' => 'boolean',
            'is_active' => 'boolean',
            'settings' => 'array',
            'translations' => 'array',
        ];
    }

    public function terms(): HasMany
    {
        return $this->hasMany(Term::class);
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'collection_taxonomy');
    }
}

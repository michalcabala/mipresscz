<?php

namespace MiPressCz\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use MiPressCz\Core\Concerns\HasOrigin;

class Term extends Model
{
    use HasFactory, HasOrigin, HasUlids, SoftDeletes;

    protected $fillable = [
        'taxonomy_id',
        'locale',
        'origin_id',
        'parent_id',
        'title',
        'slug',
        'description',
        'data',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function taxonomy(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Term::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Term::class, 'parent_id');
    }

    public function entries(): MorphToMany
    {
        return $this->morphedByMany(Entry::class, 'termable', 'termables')
            ->withPivot('order');
    }

    public function scopeForLocale(Builder $query, ?string $locale = null): Builder
    {
        return $query->where('locale', $locale ?? app()->getLocale());
    }

    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order');
    }
}

<?php

namespace MiPressCz\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blueprint extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'collection_id',
        'name',
        'title',
        'handle',
        'fields',
        'is_default',
        'icon',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'fields' => 'array',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getTranslatableFields(): array
    {
        return collect($this->fields ?? [])
            ->filter(fn (array $field) => $field['translatable'] ?? false)
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getNonTranslatableFields(): array
    {
        return collect($this->fields ?? [])
            ->reject(fn (array $field) => $field['translatable'] ?? false)
            ->reject(fn (array $field) => ($field['type'] ?? '') === 'section_break')
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getFieldsBySection(string $section): array
    {
        return collect($this->fields ?? [])
            ->filter(fn (array $field) => ($field['section'] ?? 'main') === $section)
            ->sortBy('order')
            ->values()
            ->all();
    }
}

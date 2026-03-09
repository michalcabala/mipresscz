<?php

namespace MiPressCz\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class GlobalSet extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'global_sets';

    protected $fillable = [
        'name',
        'title',
        'handle',
        'fields',
        'data',
        'locale',
        'origin_id',
    ];

    protected function casts(): array
    {
        return [
            'fields' => 'array',
            'data' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (GlobalSet $globalSet) {
            Cache::forget("global_set.{$globalSet->handle}.{$globalSet->locale}");
        });
    }

    public function origin(): BelongsTo
    {
        return $this->belongsTo(GlobalSet::class, 'origin_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(GlobalSet::class, 'origin_id');
    }

    public static function findByHandle(string $handle, ?string $locale = null): ?static
    {
        $locale ??= app()->getLocale();

        return Cache::rememberForever("global_set.{$handle}.{$locale}", function () use ($handle, $locale) {
            return static::query()
                ->where('handle', $handle)
                ->where('locale', $locale)
                ->first()
                ?? static::query()
                    ->where('handle', $handle)
                    ->whereNull('origin_id')
                    ->first();
        });
    }

    public static function getValue(string $handle, string $key, mixed $default = null): mixed
    {
        $set = static::findByHandle($handle);

        return data_get($set?->data, $key, $default);
    }
}

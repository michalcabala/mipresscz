<?php

namespace MiPressCz\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use MiPressCz\Core\Concerns\HasOrigin;

class GlobalSet extends Model
{
    use HasFactory, HasOrigin, HasUlids;

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

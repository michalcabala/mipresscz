<?php

namespace MiPressCz\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $table = 'settings';

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    protected static function booted(): void
    {
        static::saved(function (self $setting): void {
            Cache::forget("setting.{$setting->key}");
        });

        static::deleted(function (self $setting): void {
            Cache::forget("setting.{$setting->key}");
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("setting.{$key}", function () use ($key, $default): mixed {
            return static::query()->find($key)?->value ?? $default;
        });
    }

    public static function set(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }
}

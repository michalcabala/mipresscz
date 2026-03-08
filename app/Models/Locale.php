<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locale extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'code',
        'name',
        'native_name',
        'flag',
        'is_default',
        'is_active',
        'is_admin_available',
        'is_frontend_available',
        'direction',
        'date_format',
        'url_prefix',
        'fallback_locale',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'is_admin_available' => 'boolean',
            'is_frontend_available' => 'boolean',
        ];
    }

    public function getFlagUrlAttribute(): ?string
    {
        return $this->flag ? asset("assets/flags/{$this->flag}") : null;
    }
}

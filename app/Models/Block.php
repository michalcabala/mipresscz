<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'name',
        'title',
        'handle',
        'fields',
        'icon',
        'description',
        'preview_image',
        'category',
        'is_active',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'fields' => 'array',
            'is_active' => 'boolean',
        ];
    }
}

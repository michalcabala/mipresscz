<?php

namespace MiPressCz\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Revision extends Model
{
    use HasUlids;

    public $timestamps = false;

    protected $fillable = [
        'entry_id',
        'user_id',
        'title',
        'data',
        'status',
        'message',
        'is_current',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'is_current' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(Entry::class);
    }

    /** @phpstan-ignore-next-line */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', \App\Models\User::class));
    }
}

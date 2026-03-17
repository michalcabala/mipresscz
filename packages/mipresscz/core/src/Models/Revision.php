<?php

declare(strict_types=1);

namespace MiPressCz\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use MiPressCz\Core\Enums\RevisionType;

class Revision extends Model
{
    use HasUlids;
    use SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'type',
        'note',
        'revision_number',
        'content',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'type' => RevisionType::class,
            'created_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function revisionable(): MorphTo
    {
        return $this->morphTo();
    }

    /** @phpstan-ignore-next-line */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', \App\Models\User::class), 'user_id');
    }
}

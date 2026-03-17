<?php

declare(strict_types=1);

namespace MiPressCz\Core\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Arr;
use MiPressCz\Core\Enums\RevisionType;
use MiPressCz\Core\Models\Revision;

/**
 * @mixin Model
 */
trait HasRevisions
{
    protected bool $suppressAutomaticRevisions = false;

    public function revisions(): MorphMany
    {
        return $this->morphMany(Revision::class, 'revisionable')
            ->orderByDesc('revision_number')
            ->orderByDesc('created_at');
    }

    public function latestRevision(): MorphOne
    {
        return $this->morphOne(Revision::class, 'revisionable')->latestOfMany('revision_number');
    }

    public function createRevision(RevisionType $type, ?string $note = null): Revision
    {
        /** @var Revision $revision */
        $revision = $this->getConnection()->transaction(function () use ($type, $note): Revision {
            $nextRevisionNumber = ((int) $this->revisions()->withTrashed()->lockForUpdate()->max('revision_number')) + 1;

            return $this->revisions()->create([
                'user_id' => auth()->id(),
                'type' => $type,
                'note' => $note,
                'revision_number' => $nextRevisionNumber,
                'content' => $this->toRevisionSnapshot(),
                'created_at' => now(),
            ]);
        });

        return $revision;
    }

    public function shouldCreateAutomaticRevisions(): bool
    {
        return ! $this->suppressAutomaticRevisions;
    }

    /**
     * @template TReturn
     *
     * @param  callable(): TReturn  $callback
     * @return TReturn
     */
    public function withoutAutomaticRevisions(callable $callback): mixed
    {
        $previousState = $this->suppressAutomaticRevisions;
        $this->suppressAutomaticRevisions = true;

        try {
            return $callback();
        } finally {
            $this->suppressAutomaticRevisions = $previousState;
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function toRevisionSnapshot(): array
    {
        return Arr::except($this->attributesToArray(), [
            'created_at',
            'updated_at',
            'deleted_at',
        ]);
    }
}

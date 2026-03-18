<?php

declare(strict_types=1);

namespace MiPressCz\Core\Observers;

use MiPressCz\Core\Events\EntryDeleted;
use MiPressCz\Core\Events\EntrySaved;
use MiPressCz\Core\Events\EntrySaving;
use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Enums\RevisionType;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Services\RevisionService;

class EntryObserver
{
    /**
     * Fired before create or update.
     * Returning false from a listener that calls $event->cancel() will abort the save.
     */
    public function saving(Entry $entry): ?bool
    {
        $event = new EntrySaving($entry);
        event($event);

        if ($event->isCancelled()) {
            return false;
        }

        return null;
    }

    /**
     * Fired after create or update.
     */
    public function saved(Entry $entry): void
    {
        // wasRecentlyCreated is only reliable immediately after insert;
        // dispatch EntrySaved from created()/updated() hooks instead.
    }

    public function updated(Entry $entry): void
    {
        EntrySaved::dispatch($entry, false);

        if (! $entry->shouldCreateAutomaticRevisions()) {
            return;
        }

        app(RevisionService::class)->createRevision(
            $entry,
            $entry->status === EntryStatus::Published ? RevisionType::Published : RevisionType::Draft,
        );
    }

    public function created(Entry $entry): void
    {
        EntrySaved::dispatch($entry, true);

        if (! $entry->shouldCreateAutomaticRevisions()) {
            return;
        }

        app(RevisionService::class)->createRevision(
            $entry,
            $entry->status === EntryStatus::Published ? RevisionType::Published : RevisionType::Draft,
        );
    }

    public function deleted(Entry $entry): void
    {
        EntryDeleted::dispatch($entry);
    }
}

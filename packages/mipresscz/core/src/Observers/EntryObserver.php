<?php

namespace MiPressCz\Core\Observers;

use MiPressCz\Core\Events\EntryDeleted;
use MiPressCz\Core\Events\EntrySaved;
use MiPressCz\Core\Events\EntrySaving;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\Revision;

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

        if (! $entry->collection?->revisions_enabled) {
            return;
        }

        // Mark all existing revisions as not current
        $entry->revisions()->where('is_current', true)->update(['is_current' => false]);

        // Create new revision
        Revision::create([
            'entry_id' => $entry->id,
            'user_id' => auth()->id() ?? $entry->author_id,
            'title' => $entry->title,
            'data' => $entry->data,
            'content' => $entry->content,
            'status' => $entry->status->value,
            'action' => 'revision',
            'is_current' => true,
            'created_at' => now(),
        ]);

        // Prune old revisions (keep max 50)
        $this->pruneRevisions($entry);
    }

    public function created(Entry $entry): void
    {
        EntrySaved::dispatch($entry, true);

        if (! $entry->collection?->revisions_enabled) {
            return;
        }

        Revision::create([
            'entry_id' => $entry->id,
            'user_id' => auth()->id() ?? $entry->author_id,
            'title' => $entry->title,
            'data' => $entry->data,
            'content' => $entry->content,
            'status' => $entry->status->value,
            'action' => 'revision',
            'is_current' => true,
            'created_at' => now(),
        ]);
    }

    public function deleted(Entry $entry): void
    {
        EntryDeleted::dispatch($entry);
    }

    private function pruneRevisions(Entry $entry): void
    {
        $revisionIds = $entry->revisions()
            ->orderByDesc('created_at')
            ->skip(50)
            ->take(PHP_INT_MAX)
            ->pluck('id');

        if ($revisionIds->isNotEmpty()) {
            Revision::whereIn('id', $revisionIds)->delete();
        }
    }
}

<?php

namespace App\Observers;

use App\Models\Entry;
use App\Models\Revision;

class EntryObserver
{
    public function updated(Entry $entry): void
    {
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
            'status' => $entry->status->value,
            'is_current' => true,
            'created_at' => now(),
        ]);

        // Prune old revisions (keep max 50)
        $this->pruneRevisions($entry);
    }

    public function created(Entry $entry): void
    {
        if (! $entry->collection?->revisions_enabled) {
            return;
        }

        Revision::create([
            'entry_id' => $entry->id,
            'user_id' => auth()->id() ?? $entry->author_id,
            'title' => $entry->title,
            'data' => $entry->data,
            'status' => $entry->status->value,
            'is_current' => true,
            'created_at' => now(),
        ]);
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

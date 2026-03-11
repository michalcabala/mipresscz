<?php

namespace MiPressCz\Core\Events;

use Illuminate\Foundation\Events\Dispatchable;
use MiPressCz\Core\Models\Entry;

/**
 * Fired just before an Entry is saved (created or updated).
 *
 * Listeners may cancel the save by calling $event->cancel().
 * The observer checks $event->isCancelled() and returns false to abort.
 */
class EntrySaving
{
    use Dispatchable;

    private bool $cancelled = false;

    public function __construct(public readonly Entry $entry) {}

    /** Prevent the entry from being saved. */
    public function cancel(): void
    {
        $this->cancelled = true;
    }

    public function isCancelled(): bool
    {
        return $this->cancelled;
    }
}

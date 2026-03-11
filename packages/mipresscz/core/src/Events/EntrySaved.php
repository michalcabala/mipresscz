<?php

namespace MiPressCz\Core\Events;

use Illuminate\Foundation\Events\Dispatchable;
use MiPressCz\Core\Models\Entry;

/**
 * Fired after an Entry has been successfully saved (created or updated).
 *
 * @property bool $wasCreated True if the entry was just created, false if updated.
 */
class EntrySaved
{
    use Dispatchable;

    public function __construct(
        public readonly Entry $entry,
        public readonly bool $wasCreated,
    ) {}
}

<?php

namespace MiPressCz\Core\Events;

use Illuminate\Foundation\Events\Dispatchable;
use MiPressCz\Core\Models\Entry;

class EntryDeleted
{
    use Dispatchable;

    public function __construct(
        public readonly Entry $entry,
    ) {}
}

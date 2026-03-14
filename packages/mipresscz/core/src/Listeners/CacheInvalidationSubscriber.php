<?php

namespace MiPressCz\Core\Listeners;

use Illuminate\Events\Dispatcher;
use MiPressCz\Core\Events\EntryDeleted;
use MiPressCz\Core\Events\EntrySaved;
use MiPressCz\Core\Services\CacheService;

class CacheInvalidationSubscriber
{
    public function __construct(
        private CacheService $cache,
    ) {}

    public function handleEntrySaved(EntrySaved $event): void
    {
        $entry = $event->entry;

        if ($entry->uri !== null) {
            $this->cache->flushEntry($entry->uri, $entry->locale);
        }

        // Homepage change affects the root page
        if ($entry->is_homepage || $entry->getOriginal('is_homepage')) {
            $this->cache->flushEntry('/', $entry->locale);
        }

        // Navigation may show this entry
        $this->cache->flushNav();
    }

    public function handleEntryDeleted(EntryDeleted $event): void
    {
        $entry = $event->entry;

        if ($entry->uri !== null) {
            $this->cache->flushEntry($entry->uri, $entry->locale);
        }

        if ($entry->is_homepage) {
            $this->cache->flushEntry('/');
        }

        $this->cache->flushNav();
    }

    /**
     * @return array<string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            EntrySaved::class => 'handleEntrySaved',
            EntryDeleted::class => 'handleEntryDeleted',
        ];
    }
}

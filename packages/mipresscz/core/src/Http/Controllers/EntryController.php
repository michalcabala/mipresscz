<?php

namespace MiPressCz\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use MiPressCz\Core\Models\Entry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EntryController
{
    /**
     * Mason brick classes to use when rendering entry content.
     * Host applications should populate this in their service provider,
     * e.g. EntryController::$brickClasses = BrickCollection::all();
     *
     * @var array<int, class-string<\Awcodes\Mason\Brick>>
     */
    public static array $brickClasses = [];

    public function show(Request $request): View
    {
        $uri = '/'.ltrim((string) $request->route('uri', ''), '/');
        $locale = app()->getLocale();

        $entry = Entry::query()
            ->with(['collection', 'blueprint', 'author', 'terms'])
            ->published()
            ->where('uri', $uri)
            ->where('locale', $locale)
            ->first();

        if (! $entry) {
            $entry = Entry::query()
                ->with(['collection', 'blueprint', 'author', 'terms'])
                ->published()
                ->where('uri', $uri)
                ->first();
        }

        if (! $entry) {
            throw new NotFoundHttpException;
        }

        $viewName = $this->resolveView($entry);

        return view($viewName, [
            'entry' => $entry,
            'collection' => $entry->collection,
            'blueprint' => $entry->blueprint,
            'bricks' => static::$brickClasses,
        ]);
    }

    protected function resolveView(Entry $entry): string
    {
        $collection = $entry->collection->handle;
        $blueprint = $entry->blueprint->handle;

        // Try: entries/{collection}/{blueprint}
        if (view()->exists("entries.{$collection}.{$blueprint}")) {
            return "entries.{$collection}.{$blueprint}";
        }

        // Try: entries/{collection}/show
        if (view()->exists("entries.{$collection}.show")) {
            return "entries.{$collection}.show";
        }

        // Try: app-level fallback entries/show
        if (view()->exists('entries.show')) {
            return 'entries.show';
        }

        // Package fallback
        return 'mipresscz-core::entries.show';
    }
}

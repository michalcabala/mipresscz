<?php

namespace MiPressCz\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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

        $with = ['collection', 'blueprint', 'author', 'terms', 'featuredImage', 'metaOgImage', 'translations', 'origin.translations'];

        $entry = Entry::query()
            ->with($with)
            ->published()
            ->where('uri', $uri)
            ->where('locale', $locale)
            ->first();

        if (! $entry) {
            $entry = Entry::query()
                ->with($with)
                ->published()
                ->where('uri', $uri)
                ->first();
        }

        // Homepage fallback: when visiting '/', look for the entry marked as homepage.
        if (! $entry && $uri === '/') {
            $entry = Entry::query()
                ->with($with)
                ->published()
                ->where('is_homepage', true)
                ->where('locale', $locale)
                ->first()
                ?? Entry::query()
                    ->with($with)
                    ->published()
                    ->where('is_homepage', true)
                    ->first();
        }

        if (! $entry) {
            throw new NotFoundHttpException;
        }

        $viewName = $this->resolveView($entry);
        $defaultLocale = locales()->getDefaultCode();
        $canonicalUrl = $this->buildEntryUrl($entry, $defaultLocale);
        $hreflangLinks = $this->buildHreflangLinks($entry, $defaultLocale);

        return view($viewName, [
            'entry' => $entry,
            'collection' => $entry->collection,
            'blueprint' => $entry->blueprint,
            'bricks' => static::$brickClasses,
            'canonicalUrl' => $canonicalUrl,
            'hreflangLinks' => $hreflangLinks,
        ]);
    }

    /** Build the canonical URL for the given entry. */
    protected function buildEntryUrl(Entry $entry, string $defaultLocale): string
    {
        if ($entry->locale === $defaultLocale) {
            return url($entry->uri);
        }

        return url('/'.$entry->locale.$entry->uri);
    }

    /**
     * Collect all locale variants (self + siblings) for hreflang.
     *
     * @return Collection<string, array{locale: string, url: string}>
     */
    protected function buildHreflangLinks(Entry $entry, string $defaultLocale): Collection
    {
        // If this entry is a translation itself, use the origin's sibling set
        if ($entry->origin_id && $entry->origin) {
            $variants = $entry->origin->translations->push($entry->origin);
        } else {
            $variants = $entry->translations->push($entry);
        }

        return $variants
            ->unique('locale')
            ->mapWithKeys(fn (Entry $variant): array => [
                $variant->locale => [
                    'locale' => $variant->locale,
                    'url' => $this->buildEntryUrl($variant, $defaultLocale),
                ],
            ]);
    }

    public function resolveView(Entry $entry): string
    {
        $collection = $entry->collection->handle;
        $blueprint = $entry->blueprint->handle;

        // Template views take priority — check active template namespace first.
        if ($entry->is_homepage && view()->exists('template::pages.home')) {
            return 'template::pages.home';
        }

        if (view()->exists("template::{$collection}.{$blueprint}")) {
            return "template::{$collection}.{$blueprint}";
        }

        if (view()->exists("template::{$collection}.show")) {
            return "template::{$collection}.show";
        }

        if (view()->exists('template::pages.page')) {
            return 'template::pages.page';
        }

        // App-level views fallback.
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

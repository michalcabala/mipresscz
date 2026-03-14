<?php

namespace MiPressCz\Core\Http\Controllers;

use Illuminate\View\View;
use MiPressCz\Core\Models\Entry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PreviewController
{
    public function show(string $token): View
    {
        $entry = Entry::query()
            ->with(['collection', 'blueprint', 'author', 'terms', 'featuredImage', 'metaOgImage', 'translations', 'origin.translations'])
            ->where('preview_token', $token)
            ->first();

        if (! $entry || ! $entry->isPreviewTokenValid($token)) {
            throw new NotFoundHttpException;
        }

        app()->setLocale($entry->locale);

        $viewName = (new EntryController)->resolveView($entry);
        $defaultLocale = locales()->getDefaultCode();

        return view($viewName, [
            'entry' => $entry,
            'collection' => $entry->collection,
            'blueprint' => $entry->blueprint,
            'bricks' => EntryController::$brickClasses,
            'canonicalUrl' => url($entry->uri),
            'hreflangLinks' => collect(),
            'isPreview' => true,
        ]);
    }
}

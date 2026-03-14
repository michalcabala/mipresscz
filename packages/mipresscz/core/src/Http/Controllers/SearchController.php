<?php

namespace MiPressCz\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use MiPressCz\Core\Models\Entry;

class SearchController
{
    public function __invoke(Request $request): View
    {
        $query = (string) $request->input('q', '');
        $locale = app()->getLocale();
        $results = collect();

        if (mb_strlen($query) >= 2) {
            $results = Entry::search($query)
                ->query(fn ($builder) => $builder->published()->where('locale', $locale)->with(['collection']))
                ->paginate(10)
                ->appends(['q' => $query]);
        }

        $viewName = view()->exists('template::search') ? 'template::search' : 'mipresscz-core::search';

        return view($viewName, [
            'query' => $query,
            'results' => $results,
        ]);
    }
}

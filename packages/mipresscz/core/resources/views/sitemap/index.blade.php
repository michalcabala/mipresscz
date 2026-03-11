<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
    @foreach($entries as $entry)
        @php
            $loc = $entry->locale === $defaultLocale
                ? url($entry->uri)
                : url('/'.$entry->locale.$entry->uri);
        @endphp
        <url>
            <loc>{{ $loc }}</loc>
            @if($entry->updated_at)
                <lastmod>{{ $entry->updated_at->toAtomString() }}</lastmod>
            @endif
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach
</urlset>

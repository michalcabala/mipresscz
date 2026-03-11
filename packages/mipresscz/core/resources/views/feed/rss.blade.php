<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ config('app.name') }}</title>
        <link>{{ url('/') }}</link>
        <description>{{ config('app.name') }}</description>
        <language>{{ $locale }}</language>
        <lastBuildDate>{{ now()->toRfc1123String() }}</lastBuildDate>
        <atom:link href="{{ url('/feed.xml') }}" rel="self" type="application/rss+xml"/>

        @foreach($entries as $entry)
            @php
                $entryUrl = $entry->locale === $defaultLocale
                    ? url($entry->uri)
                    : url('/'.$entry->locale.$entry->uri);
            @endphp
            <item>
                <title><![CDATA[{{ $entry->title }}]]></title>
                <link>{{ $entryUrl }}</link>
                <guid isPermaLink="true">{{ $entryUrl }}</guid>
                @if($entry->meta_description)
                    <description><![CDATA[{{ $entry->meta_description }}]]></description>
                @endif
                @if($entry->published_at)
                    <pubDate>{{ $entry->published_at->toRfc1123String() }}</pubDate>
                @endif
            </item>
        @endforeach
    </channel>
</rss>

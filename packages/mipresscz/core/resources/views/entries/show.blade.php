<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @php
        $seoTitle = $entry->meta_title ?: $entry->title;
        $seoDescription = $entry->meta_description ?: null;
        $ogImage = $entry->metaOgImage ?? $entry->featuredImage ?? null;
        $ogImageUrl = $ogImage?->url ?? null;
    @endphp

    <title>{{ $seoTitle }}</title>

    @if($seoDescription)
        <meta name="description" content="{{ $seoDescription }}">
    @endif

    {{-- Open Graph --}}
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    @if($seoDescription)
        <meta property="og:description" content="{{ $seoDescription }}">
    @endif
    @if($ogImageUrl)
        <meta property="og:image" content="{{ $ogImageUrl }}">
    @endif

    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ $canonicalUrl }}">

    {{-- Hreflang alternate links --}}
    @if($hreflangLinks->count() > 1)
        @foreach($hreflangLinks as $link)
            <link rel="alternate" hreflang="{{ $link['locale'] }}" href="{{ $link['url'] }}">
        @endforeach
        <link rel="alternate" hreflang="x-default" href="{{ $hreflangLinks->first()['url'] }}">
    @endif
</head>
<body>
    <article>
        <h1>{{ $entry->title }}</h1>

        @if($entry->published_at)
            <time datetime="{{ $entry->published_at->toIso8601String() }}">
                {{ $entry->published_at->isoFormat('LL') }}
            </time>
        @endif

        @if($entry->author)
            <p>{{ $entry->author->name }}</p>
        @endif

        @if(!empty($entry->content))
            <div class="entry-content">
                {!! mason(content: $entry->content, bricks: $bricks)->toHtml() !!}
            </div>
        @elseif(!empty($entry->data['content']))
            {{-- Legacy fallback for entries not yet migrated to Mason --}}
            <div class="entry-content prose">
                {!! $entry->data['content'] !!}
            </div>
        @endif

        @if($entry->terms->isNotEmpty())
            <div>
                @foreach($entry->terms as $term)
                    <span>{{ $term->title }}</span>
                @endforeach
            </div>
        @endif
    </article>
</body>
</html>

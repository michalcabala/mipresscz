<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $entry->title }}</title>
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

@extends('template::layouts.app')

@section('title', ($entry->meta_title ?? null) ?: ($entry->title ?? config('app.name')))
@section('description', $entry->meta_description ?? '')

@section('content')
<article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    @if($entry->featured_image_id ?? null)
    <div class="mb-8 rounded-xl overflow-hidden">
        <x-curator-glider
            :media="$entry->featured_image_id"
            class="w-full h-64 object-cover"
            width="900"
            height="400"
        />
    </div>
    @endif

    <header class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl">
            {{ $entry->title }}
        </h1>

        @if($entry->published_at ?? null)
        <p class="mt-3 text-sm text-gray-500">
            {{ \Illuminate\Support\Carbon::parse($entry->published_at)->isoFormat('D. MMMM YYYY') }}
            @if($entry->author ?? null)
                &middot; {{ $entry->author->name }}
            @endif
        </p>
        @endif
    </header>

    @if(!empty($entry->content))
    <div class="prose prose-lg max-w-none">
        {!! mason(content: $entry->content, bricks: $bricks ?? [])->toHtml() !!}
    </div>
    @endif

</article>
@endsection

@extends('template::layouts.app')

@section('title', ($entry->meta_title ?? null) ?: ($entry->title ?? config('app.name')))
@section('description', $entry->meta_description ?? '')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

    {{-- Hero --}}
    <div class="text-center mb-16">
        @if($entry->featured_image_id ?? null)
        <div class="mb-8 rounded-2xl overflow-hidden max-h-96">
            <x-curator-glider
                :media="$entry->featured_image_id"
                class="w-full h-full object-cover"
                width="1280"
                height="480"
            />
        </div>
        @endif

        <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
            {{ $entry->title }}
        </h1>
    </div>

    {{-- Content --}}
    @if(!empty($entry->content))
    <div class="prose prose-lg max-w-4xl mx-auto">
        {!! mason(content: $entry->content, bricks: \Awcodes\Mason\BrickCollection::make($bricks ?? []))->toHtml() !!}
    </div>
    @endif

</div>
@endsection

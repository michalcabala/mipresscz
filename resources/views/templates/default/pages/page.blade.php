@extends('template::layouts.app')

@section('title', ($entry->meta_title ?? null) ?: ($entry->title ?? config('app.name')))
@section('description', $entry->meta_description ?? '')

@section('content')

{{-- Page hero --}}
<div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
        <div class="max-w-3xl">
            <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 dark:text-white leading-tight tracking-tight mb-4">
                {{ $entry->title }}
            </h1>
            @if($entry->meta_description ?? null)
            <p class="text-lg text-gray-600 dark:text-gray-400">{{ $entry->meta_description }}</p>
            @endif
        </div>
    </div>
</div>

@if($entry->featured_image_id ?? null)
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 mb-0">
    <div class="rounded-2xl overflow-hidden shadow-xl max-h-[480px]">
        <x-curator-glider
            :media="$entry->featured_image_id"
            class="w-full h-full object-cover"
            width="1280"
            height="480"
        />
    </div>
</div>
@endif

{{-- Content --}}
@if(!empty($entry->content))
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {!! mason(content: $entry->content, bricks: $bricks ?? [])->toHtml() !!}
</div>
@endif

@endsection

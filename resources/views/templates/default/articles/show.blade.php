@extends('template::layouts.app')

@section('title', ($entry->meta_title ?? null) ?: ($entry->title ?? config('app.name')))
@section('description', $entry->meta_description ?? '')

@section('content')

{{-- Article header --}}
<div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-8">
            <a href="{{ url('/') }}" class="hover:text-gray-900 dark:hover:text-white transition-colors">{{ __('Domů') }}</a>
            <span>/</span>
            <span class="text-gray-900 dark:text-white">{{ $entry->title }}</span>
        </nav>

        <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 dark:text-white leading-tight tracking-tight mb-6">
            {{ $entry->title }}
        </h1>

        @if($entry->meta_description ?? null)
        <p class="text-xl text-gray-600 dark:text-gray-400 leading-relaxed mb-8">{{ $entry->meta_description }}</p>
        @endif

        {{-- Author / date meta --}}
        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
            @if($entry->published_at ?? null)
            <time datetime="{{ \Carbon\Carbon::parse($entry->published_at)->toIso8601String() }}" class="font-mono">
                {{ \Carbon\Carbon::parse($entry->published_at)->translatedFormat('j. F Y') }}
            </time>
            @endif
            @if($entry->author ?? null)
            <span class="flex items-center gap-2">
                <span class="w-1 h-1 rounded-full bg-gray-400"></span>
                {{ $entry->author->name }}
            </span>
            @endif
        </div>
    </div>
</div>

{{-- Featured image --}}
@if($entry->featured_image_id ?? null)
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">
    <div class="rounded-2xl overflow-hidden shadow-xl">
        <x-curator-glider
            :media="$entry->featured_image_id"
            class="w-full h-auto"
            width="1024"
            height="576"
        />
    </div>
</div>
@endif

{{-- Article body --}}
@if(!empty($entry->content))
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="prose prose-lg dark:prose-invert max-w-none">
        {!! mason(content: $entry->content, bricks: $bricks ?? [])->toHtml() !!}
    </div>
</div>
@endif

{{-- Related articles (same collection, different entry) --}}
@php
    $relatedArticles = \MiPressCz\Core\Models\Entry::query()
        ->with(['featuredImage'])
        ->whereHas('collection', fn ($q) => $q->where('handle', 'articles'))
        ->published()
        ->where('locale', app()->getLocale())
        ->where('id', '!=', $entry->id)
        ->orderByDesc('published_at')
        ->limit(2)
        ->get();
@endphp

@if($relatedArticles->isNotEmpty())
<section class="border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">{{ __('Další příspěvky') }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
            @foreach($relatedArticles as $related)
            <article class="group flex flex-col bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden hover:border-blue-300 dark:hover:border-blue-600 transition-colors">
                @if($related->featured_image_id ?? null)
                <div class="aspect-video overflow-hidden bg-gray-100 dark:bg-gray-700">
                    <x-curator-glider
                        :media="$related->featured_image_id"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        width="600"
                        height="338"
                    />
                </div>
                @endif
                <div class="p-6">
                    @if($related->published_at ?? null)
                    <time class="text-xs font-mono text-gray-500 dark:text-gray-400">
                        {{ \Carbon\Carbon::parse($related->published_at)->translatedFormat('j. F Y') }}
                    </time>
                    @endif
                    <h3 class="mt-2 font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors leading-snug">
                        <a href="{{ url($related->uri) }}">{{ $related->title }}</a>
                    </h3>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection

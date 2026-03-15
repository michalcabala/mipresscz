@extends('template::layouts.app')

@section('title', $collection->getLocalizedTitle().' | '.config('app.name', 'miPress'))
@section('description', $collection->description ?? '')

@section('content')
@php
    $gridUrl = request()->fullUrlWithQuery(['view' => 'grid', 'page' => null]);
    $listUrl = request()->fullUrlWithQuery(['view' => 'list', 'page' => null]);
@endphp

<section class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-18">
        <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
            <a href="{{ url('/') }}" class="hover:text-gray-900 dark:hover:text-white transition-colors">{{ __('Domů') }}</a>
            <span>/</span>
            <span class="text-gray-900 dark:text-white">{{ $collection->getLocalizedTitle() }}</span>
        </nav>

        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-blue-600 dark:text-blue-400 mb-3">{{ __('Archiv kolekce') }}</p>
                <h1 class="text-4xl sm:text-5xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $collection->getLocalizedTitle() }}</h1>
                @if($collection->description)
                    <p class="mt-4 text-lg leading-relaxed text-gray-600 dark:text-gray-400">{{ $collection->description }}</p>
                @endif
            </div>

            <div class="inline-flex rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-1 shadow-sm">
                <a href="{{ $gridUrl }}" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium transition-colors {{ $viewMode === 'grid' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white' }}">
                    <span>{{ __('Mřížka') }}</span>
                </a>
                <a href="{{ $listUrl }}" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium transition-colors {{ $viewMode === 'list' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white' }}">
                    <span>{{ __('Seznam') }}</span>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
    @if($entries->isEmpty())
        <div class="rounded-3xl border border-dashed border-gray-300 dark:border-gray-700 px-8 py-16 text-center bg-white/70 dark:bg-gray-900/70">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('Archiv je zatím prázdný') }}</h2>
            <p class="mt-3 text-gray-600 dark:text-gray-400">{{ __('Jakmile budou publikované první záznamy, objeví se tady.') }}</p>
        </div>
    @elseif($viewMode === 'list')
        <div class="space-y-6">
            @foreach($entries as $entry)
                <article class="grid gap-6 overflow-hidden rounded-3xl border border-gray-200 bg-white p-6 shadow-sm transition-colors hover:border-blue-300 dark:border-gray-800 dark:bg-gray-900 dark:hover:border-blue-700 md:grid-cols-[280px,1fr]">
                    @if($entry->featured_image_id)
                        <a href="{{ url($entry->uri) }}" class="block overflow-hidden rounded-2xl bg-gray-100 dark:bg-gray-800">
                            <x-curator-glider :media="$entry->featured_image_id" class="h-full w-full object-cover" width="560" height="360" />
                        </a>
                    @endif
                    <div class="flex flex-col justify-between gap-4">
                        <div>
                            <div class="flex flex-wrap items-center gap-3 text-xs uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">
                                @if($entry->published_at)
                                    <time datetime="{{ \Carbon\Carbon::parse($entry->published_at)->toIso8601String() }}">{{ \Carbon\Carbon::parse($entry->published_at)->translatedFormat('j. F Y') }}</time>
                                @endif
                                @if($entry->author)
                                    <span>{{ $entry->author->name }}</span>
                                @endif
                            </div>
                            <h2 class="mt-3 text-2xl font-semibold leading-tight text-gray-900 dark:text-white">
                                <a href="{{ url($entry->uri) }}" class="transition-colors hover:text-blue-600 dark:hover:text-blue-400">{{ $entry->title }}</a>
                            </h2>
                            <p class="mt-3 text-base leading-relaxed text-gray-600 dark:text-gray-400">{{ $entry->excerpt ?: ($entry->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($entry->getPlainTextContent()), 180)) }}</p>
                        </div>
                        <div>
                            <a href="{{ url($entry->uri) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 transition-colors hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">{{ __('Číst detail') }}</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
            @foreach($entries as $entry)
                <article class="group flex h-full flex-col overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm transition-colors hover:border-blue-300 dark:border-gray-800 dark:bg-gray-900 dark:hover:border-blue-700">
                    @if($entry->featured_image_id)
                        <a href="{{ url($entry->uri) }}" class="block aspect-[16/10] overflow-hidden bg-gray-100 dark:bg-gray-800">
                            <x-curator-glider :media="$entry->featured_image_id" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" width="720" height="450" />
                        </a>
                    @endif
                    <div class="flex flex-1 flex-col p-6">
                        <div class="flex flex-wrap items-center gap-3 text-xs uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">
                            @if($entry->published_at)
                                <time datetime="{{ \Carbon\Carbon::parse($entry->published_at)->toIso8601String() }}">{{ \Carbon\Carbon::parse($entry->published_at)->translatedFormat('j. F Y') }}</time>
                            @endif
                            @if($entry->author)
                                <span>{{ $entry->author->name }}</span>
                            @endif
                        </div>
                        <h2 class="mt-3 text-xl font-semibold leading-tight text-gray-900 dark:text-white">
                            <a href="{{ url($entry->uri) }}" class="transition-colors group-hover:text-blue-600 dark:group-hover:text-blue-400">{{ $entry->title }}</a>
                        </h2>
                        <p class="mt-3 flex-1 text-sm leading-relaxed text-gray-600 dark:text-gray-400">{{ $entry->excerpt ?: ($entry->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($entry->getPlainTextContent()), 140)) }}</p>
                        <div class="mt-5">
                            <a href="{{ url($entry->uri) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 transition-colors hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">{{ __('Číst detail') }}</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    @if($entries->hasPages())
        <div class="mt-10">
            {{ $entries->links() }}
        </div>
    @endif
</section>
@endsection

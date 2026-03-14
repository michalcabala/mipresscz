@extends('template::layouts.app')

@section('title', __('content.search.title'))

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold mb-8">{{ __('content.search.title') }}</h1>

    <form action="{{ route('search') }}" method="GET" class="mb-8">
        <div class="flex gap-2">
            <input
                type="search"
                name="q"
                value="{{ e($query) }}"
                placeholder="{{ __('content.search.placeholder') }}"
                class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                minlength="2"
                autofocus
            />
            <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2 text-white hover:bg-blue-700 transition-colors">
                {{ __('content.search.button') }}
            </button>
        </div>
    </form>

    @if(mb_strlen($query) >= 2)
        @if($results->isEmpty())
            <p class="text-gray-500 dark:text-gray-400">
                {{ __('content.search.no_results', ['query' => e($query)]) }}
            </p>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                {{ trans_choice('content.search.results_count', $results->total(), ['count' => $results->total(), 'query' => e($query)]) }}
            </p>

            <div class="space-y-6">
                @foreach($results as $entry)
                    <article class="rounded-lg border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-shadow">
                        <h2 class="text-xl font-semibold mb-1">
                            <a href="{{ $entry->getFullUrl() }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                {{ $entry->title }}
                            </a>
                        </h2>
                        @if($entry->collection)
                            <span class="inline-block text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-700 px-2 py-0.5 text-gray-600 dark:text-gray-300 mb-2">
                                {{ $entry->collection->title }}
                            </span>
                        @endif
                        @if($entry->meta_description)
                            <p class="text-gray-600 dark:text-gray-400 text-sm">{{ Str::limit($entry->meta_description, 160) }}</p>
                        @endif
                    </article>
                @endforeach
            </div>

            @if($results->hasPages())
                <div class="mt-8">
                    {{ $results->links() }}
                </div>
            @endif
        @endif
    @elseif(mb_strlen($query) > 0)
        <p class="text-gray-500 dark:text-gray-400">{{ __('content.search.min_length') }}</p>
    @endif
</div>
@endsection

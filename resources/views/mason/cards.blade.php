<div class="mason-brick mason-cards py-16 px-4">
    @if(($heading ?? null) || ($subheading ?? null))
    <div class="text-center mb-12">
        @if($heading ?? null)
        <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-3">{{ $heading }}</h2>
        @endif
        @if($subheading ?? null)
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">{{ $subheading }}</p>
        @endif
    </div>
    @endif

    @if(!empty($items ?? []))
    @php $gridClass = ($columns ?? '3') === '2' ? 'grid-cols-1 sm:grid-cols-2' : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'; @endphp
    <div class="grid {{ $gridClass }} gap-8 max-w-6xl mx-auto">
        @foreach($items as $item)
        <div class="group flex flex-col bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-8 hover:border-blue-300 dark:hover:border-blue-700 hover:shadow-md transition-all duration-200">
            @if($item['icon'] ?? null)
            <div class="text-3xl mb-4">{{ $item['icon'] }}</div>
            @endif
            @if($item['title'] ?? null)
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $item['title'] }}</h3>
            @endif
            @if($item['description'] ?? null)
            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed flex-1">{{ $item['description'] }}</p>
            @endif
            @if(($item['url'] ?? null) && ($item['link_label'] ?? null))
            <a href="{{ $item['url'] }}" class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                {{ $item['link_label'] }}
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
            </a>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>

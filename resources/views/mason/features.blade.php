<div class="mason-brick mason-features py-16 px-4">
    @if($heading ?? null)
    <div class="text-center mb-12">
        <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-3">{{ $heading }}</h2>
        @if($subheading ?? null)
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">{{ $subheading }}</p>
        @endif
    </div>
    @endif

    @if(!empty($items ?? []))
    @php $cols = $columns ?? '3'; $gridClass = match($cols) { '2' => 'grid-cols-1 sm:grid-cols-2', '4' => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4', default => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3', }; @endphp
    <div class="grid {{ $gridClass }} gap-8 max-w-6xl mx-auto">
        @foreach($items as $item)
        <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-8 hover:border-blue-300 dark:hover:border-blue-700 transition-colors">
            @if($item['icon'] ?? null)
            <div class="text-3xl mb-4">{{ $item['icon'] }}</div>
            @endif
            @if($item['title'] ?? null)
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $item['title'] }}</h3>
            @endif
            @if($item['description'] ?? null)
            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $item['description'] }}</p>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>

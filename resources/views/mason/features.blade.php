<div class="mason-brick mason-features bg-white dark:bg-gray-950 py-24 sm:py-32 px-4">
    @if(($eyebrow ?? null) || ($heading ?? null))
    <div class="text-center mb-16">
        @if($eyebrow ?? null)
        <span class="text-blue-600 dark:text-blue-400 text-sm font-semibold font-mono uppercase tracking-widest">{{ $eyebrow }}</span>
        @endif
        @if($heading ?? null)
        <h2 class="mt-3 text-4xl sm:text-5xl font-bold text-gray-900 dark:text-white leading-tight">{{ $heading }}</h2>
        @endif
        @if($subheading ?? null)
        <p class="mt-4 text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">{{ $subheading }}</p>
        @endif
    </div>
    @endif

    @if(!empty($items ?? []))
    @php $cols = $columns ?? '3'; $gridClass = match($cols) { '2' => 'grid-cols-1 sm:grid-cols-2', '4' => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4', default => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3', }; @endphp
    <div class="grid {{ $gridClass }} gap-8 max-w-7xl mx-auto">
        @foreach($items as $item)
        <div class="group relative bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-8 hover:border-blue-300 dark:hover:border-blue-700 hover:bg-blue-50/50 dark:hover:bg-blue-950/30 transition-all duration-200">
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

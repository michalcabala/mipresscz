<div class="mason-brick mason-stats bg-blue-600 dark:bg-blue-700 py-16 px-4">
    @if($heading ?? null)
    <div class="text-center mb-10">
        <h2 class="text-3xl font-bold text-white mb-2">{{ $heading }}</h2>
        @if($subheading ?? null)
        <p class="text-blue-200">{{ $subheading }}</p>
        @endif
    </div>
    @endif

    @if(!empty($items ?? []))
    @php $count = count($items); $gridClass = match(true) { $count <= 2 => 'grid-cols-2', $count === 3 => 'grid-cols-3', default => 'grid-cols-2 lg:grid-cols-4', }; @endphp
    <div class="grid {{ $gridClass }} gap-8 max-w-5xl mx-auto text-center">
        @foreach($items as $item)
        <div>
            <div class="text-3xl sm:text-4xl font-bold font-mono text-white mb-1">{{ $item['value'] ?? '' }}</div>
            <div class="text-blue-200 text-sm font-medium">{{ $item['label'] ?? '' }}</div>
            @if($item['description'] ?? null)
            <div class="text-blue-300 text-xs mt-1">{{ $item['description'] }}</div>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>

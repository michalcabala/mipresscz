@php
    $variantClasses = match($variant ?? 'blue') {
        'dark'  => 'bg-gray-950 dark:bg-gray-900',
        'light' => 'bg-gray-50 dark:bg-gray-900',
        default => 'bg-gradient-to-br from-blue-600 to-blue-800',
    };
    $headingClass = match($variant ?? 'blue') {
        'light' => 'text-gray-900 dark:text-white',
        default => 'text-white',
    };
    $textClass = match($variant ?? 'blue') {
        'light' => 'text-gray-600 dark:text-gray-400',
        'dark'  => 'text-gray-400',
        default => 'text-blue-200',
    };
    $btnClass = match($variant ?? 'blue') {
        'blue'  => 'bg-white text-blue-700 hover:bg-blue-50',
        'dark'  => 'bg-blue-600 text-white hover:bg-blue-500',
        default => 'bg-blue-600 text-white hover:bg-blue-500',
    };
    $btnSecClass = match($variant ?? 'blue') {
        'light' => 'border-gray-300 text-gray-700 hover:border-gray-400',
        default => 'border-white/40 text-white hover:border-white',
    };
@endphp
<div class="mason-brick mason-cta {{ $variantClasses }} relative overflow-hidden py-20 px-4">
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 70% 50%, white 0%, transparent 70%)"></div>
    <div class="relative z-10 max-w-3xl mx-auto text-center">
        @if($heading ?? null)
        <h2 class="text-4xl sm:text-5xl font-bold {{ $headingClass }} mb-6">{{ $heading }}</h2>
        @endif
        @if($subheading ?? null)
        <p class="text-lg {{ $textClass }} max-w-2xl mx-auto mb-10">{{ $subheading }}</p>
        @endif
        @if(($button_label ?? null) || ($secondary_label ?? null))
        <div class="flex flex-wrap items-center justify-center gap-4">
            @if($button_label ?? null)
            <a href="{{ $button_url ?? '#' }}" class="inline-flex items-center gap-2 {{ $btnClass }} font-semibold px-8 py-4 rounded-xl transition-colors shadow-lg">
                {{ $button_label }}
            </a>
            @endif
            @if($secondary_label ?? null)
            <a href="{{ $secondary_url ?? '#' }}" class="inline-flex items-center gap-2 border-2 {{ $btnSecClass }} font-semibold px-8 py-4 rounded-xl transition-colors">
                {{ $secondary_label }}
            </a>
            @endif
        </div>
        @endif
    </div>
</div>

@php
    $navEntries = \MiPressCz\Core\Models\Entry::query()
        ->whereHas('collection', fn ($q) => $q->where('handle', 'pages'))
        ->where('is_homepage', false)
        ->published()
        ->where('locale', app()->getLocale())
        ->orderBy('title')
        ->limit(8)
        ->get(['id', 'title', 'uri']);
@endphp

<header class="sticky top-0 z-50 border-b border-gray-200/80 dark:border-gray-800/80 backdrop-blur-xl bg-white/90 dark:bg-gray-950/90">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">

            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 shrink-0 group">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-600 text-white text-sm font-bold font-mono group-hover:bg-blue-500 transition-colors">m/</span>
                <span class="font-semibold text-gray-900 dark:text-white tracking-tight hidden sm:inline">{{ config('app.name', 'miPress') }}</span>
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden lg:flex items-center gap-1 flex-1 justify-center">
                @include('template::partials.nav', ['navEntries' => $navEntries])
            </nav>

            {{-- Actions --}}
            <div class="flex items-center gap-1 shrink-0">
                {{-- Theme toggle --}}
                <button
                    onclick="miPressToggleTheme()"
                    type="button"
                    title="{{ __('Přepnout tmavý / světlý režim') }}"
                    class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-800 dark:hover:text-white transition-colors"
                >
                    {{-- Moon — shown in light mode --}}
                    <svg class="w-5 h-5 dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                    </svg>
                    {{-- Sun — shown in dark mode --}}
                    <svg class="w-5 h-5 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0z" />
                    </svg>
                </button>

                {{-- Mobile hamburger --}}
                <button
                    onclick="miPressOpenMenu()"
                    type="button"
                    aria-label="{{ __('Otevřít navigaci') }}"
                    class="lg:hidden p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</header>

{{-- Fullscreen mobile nav overlay --}}
<div
    id="mobile-nav-overlay"
    class="hidden fixed inset-0 z-[100] flex flex-col bg-gray-950 lg:hidden"
    role="dialog"
    aria-modal="true"
    aria-label="{{ __('Mobilní navigace') }}"
>
    <div class="flex items-center justify-between px-4 h-16 border-b border-gray-800">
        <a href="{{ url('/') }}" onclick="miPressCloseMenu()" class="flex items-center gap-2.5">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-600 text-white text-sm font-bold font-mono">m/</span>
            <span class="font-semibold text-white">{{ config('app.name', 'miPress') }}</span>
        </a>
        <button
            onclick="miPressCloseMenu()"
            type="button"
            aria-label="{{ __('Zavřít navigaci') }}"
            class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800 transition-colors"
        >
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav class="flex flex-col items-start justify-center flex-1 gap-1 px-6 py-8">
        <a href="{{ url('/') }}" onclick="miPressCloseMenu()"
           class="block text-2xl font-semibold text-white hover:text-blue-400 transition-colors py-3 px-3 rounded-xl w-full">{{ __('Domů') }}</a>
        @foreach($navEntries as $item)
            <a href="{{ url($item->uri) }}" onclick="miPressCloseMenu()"
               class="block text-2xl font-semibold text-white hover:text-blue-400 transition-colors py-3 px-3 rounded-xl w-full">{{ $item->title }}</a>
        @endforeach
    </nav>

    <div class="px-6 pb-8 flex items-center gap-4 border-t border-gray-800 pt-6">
        <a href="{{ url('/mpcp') }}" class="text-sm text-gray-500 hover:text-white transition-colors">{{ __('Admin') }}</a>
        <button onclick="miPressToggleTheme()" class="text-sm text-gray-500 hover:text-white transition-colors">{{ __('Přepnout motiv') }}</button>
    </div>
</div>

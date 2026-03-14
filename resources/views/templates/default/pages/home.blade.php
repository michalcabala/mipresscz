@extends('template::layouts.app')

@section('title', ($entry->meta_title ?? null) ?: config('app.name', 'miPress'))
@section('description', $entry->meta_description ?? __('Moderní CMS postavené na Laravelu 12, Filamentu 5 a Tailwind CSS. Strukturovaný obsah, blokový editor a vícejazyčnost.'))

@section('content')

{{-- ===================== HERO ===================== --}}
<section class="relative overflow-hidden min-h-screen flex items-center">
    {{-- Animated background --}}
    <div class="absolute inset-0 bg-gradient-to-br from-gray-950 via-blue-950 to-gray-950 dark:from-gray-950 dark:via-blue-950 dark:to-gray-950"></div>
    <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 20% 50%, #1d4ed8 0%, transparent 60%), radial-gradient(circle at 80% 20%, #1e40af 0%, transparent 50%)"></div>
    {{-- Grid pattern overlay --}}
    <div class="absolute inset-0 opacity-5" style="background-image: linear-gradient(rgba(255,255,255,.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.1) 1px, transparent 1px); background-size: 60px 60px"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 sm:py-32 lg:py-40 w-full">
        <div class="max-w-3xl">
            {{-- Eyebrow --}}
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-blue-500/30 bg-blue-500/10 text-blue-300 text-sm font-mono mb-8">
                <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                Laravel 12 &nbsp;+&nbsp; Filament 5 &nbsp;+&nbsp; Tailwind CSS 4
            </div>

            {{-- Headline --}}
            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold text-white leading-tight tracking-tight mb-6">
                Obsah pod<br>
                <span class="bg-gradient-to-r from-blue-400 to-blue-300 bg-clip-text text-transparent">vaší kontrolou.</span>
            </h1>

            {{-- Subheadline --}}
            <p class="text-lg sm:text-xl text-gray-300 leading-relaxed max-w-2xl mb-10">
                {{ __('miPress je otevřený CMS navržený pro ty, kdo chtějí flexibilitu Laravelu bez kompromisů. Strukturovaný obsah, bloková editace a vícejazyčnost hned po instalaci.') }}
            </p>

            {{-- CTA Buttons --}}
            <div class="flex flex-wrap gap-4">
                <a href="{{ url('/mpcp') }}"
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold px-6 py-3 rounded-xl transition-colors shadow-lg shadow-blue-900/40">
                    {{ __('Začít bezplatně') }}
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                </a>
                <a href="https://github.com" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 border border-gray-600 hover:border-gray-400 text-gray-300 hover:text-white font-semibold px-6 py-3 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .5C5.65.5.5 5.65.5 12c0 5.1 3.3 9.42 7.88 10.95.58.1.79-.25.79-.56v-2c-3.2.69-3.87-1.54-3.87-1.54-.53-1.34-1.3-1.7-1.3-1.7-1.06-.72.08-.71.08-.71 1.17.08 1.79 1.2 1.79 1.2 1.04 1.78 2.72 1.27 3.38.97.1-.75.41-1.27.74-1.56-2.55-.29-5.24-1.28-5.24-5.7 0-1.26.45-2.29 1.18-3.1-.12-.29-.51-1.47.11-3.06 0 0 .97-.31 3.17 1.18a11.05 11.05 0 0 1 5.78 0c2.2-1.49 3.17-1.18 3.17-1.18.62 1.59.23 2.77.11 3.06.74.81 1.18 1.84 1.18 3.1 0 4.43-2.7 5.41-5.27 5.69.42.36.79 1.07.79 2.16v3.2c0 .31.21.67.8.56A11.512 11.512 0 0 0 23.5 12C23.5 5.65 18.35.5 12 .5z"/></svg>
                    GitHub
                </a>
            </div>
        </div>

        {{-- Decorative floating badge --}}
        <div class="absolute right-8 top-1/2 -translate-y-1/2 hidden xl:flex flex-col gap-3 opacity-60">
            @foreach([['php', 'PHP 8.3'], ['mysql', 'MySQL 8'], ['node', 'Node 22']] as [$icon, $label])
            <div class="flex items-center gap-3 bg-gray-900/60 border border-gray-700/50 rounded-xl px-4 py-3 backdrop-blur-sm">
                <span class="font-mono text-xs text-blue-400">// {{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Wave bottom --}}
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="block w-full text-white dark:text-gray-950" preserveAspectRatio="none">
            <path d="M0 60L1440 60L1440 30C1200 5 960 0 720 15C480 30 240 50 0 30L0 60Z" fill="currentColor"/>
        </svg>
    </div>
</section>

{{-- Pokud má homepage vlastní Mason obsah, zobraz ho jako první --}}
@if(!empty($entry->content))
<div class="bg-white dark:bg-gray-950">
    {!! mason(content: $entry->content, bricks: $bricks ?? [])->toHtml() !!}
</div>
@else

{{-- ===================== FEATURES ===================== --}}
<section class="bg-white dark:bg-gray-950 py-24 sm:py-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-blue-600 dark:text-blue-400 text-sm font-semibold font-mono uppercase tracking-widest">{{ __('Možnosti') }}</span>
            <h2 class="mt-3 text-4xl sm:text-5xl font-bold text-gray-900 dark:text-white leading-tight">{{ __('Vše, co váš web potřebuje') }}</h2>
            <p class="mt-4 text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">{{ __('Navrženo s důrazem na vývojářský komfort a obsahovou svobodu. Bez zbytečného omezování.') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach([
                ['icon' => '🗂️', 'title' => __('Strukturovaný obsah'), 'desc' => __('Collections, Blueprints a Entries — navrhněte datovou strukturu přesně podle svých potřeb, ne podle omezení CMS.')],
                ['icon' => '🧱', 'title' => __('Blokový editor'), 'desc' => __('Drag & drop stránky z předpřipravených Mason bloků. Hrdinská sekce, galerie, reference — vše bez kódu.')],
                ['icon' => '🌍', 'title' => __('Vícejazyčnost'), 'desc' => __('Plná i18n podpora s locale prefixem, hreflang meta tagy a automatickým přesměrováním.')],
                ['icon' => '🖼️', 'title' => __('Správa médií'), 'desc' => __('Integrovaná mediathéka s náhledy, šablonami ořezu a organizací. Nahrajte jednou, použijte kdekoliv.')],
                ['icon' => '🔐', 'title' => __('Role & Oprávnění'), 'desc' => __('SuperAdmin, Admin, Editor a Contributor — granulární správa přístupu pro celý váš tým.')],
                ['icon' => '⚡', 'title' => __('Výkon & SEO'), 'desc' => __('Vlastní meta tagy, kanonické URL, hreflang a rychlé načítání díky backendu na Laravelu 12.')],
            ] as $feature)
            <div class="group relative bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-8 hover:border-blue-300 dark:hover:border-blue-700 hover:bg-blue-50/50 dark:hover:bg-blue-950/30 transition-all duration-200">
                <div class="text-3xl mb-4">{{ $feature['icon'] }}</div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $feature['title'] }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $feature['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================== STATS ===================== --}}
<section class="bg-blue-600 dark:bg-blue-700 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
            @foreach([
                ['value' => '100%', 'label' => __('Otevřený zdrojový kód')],
                ['value' => 'Laravel 12', 'label' => __('Základ aplikace')],
                ['value' => 'Filament 5', 'label' => __('Admin panel')],
                ['value' => 'Tailwind 4', 'label' => __('CSS framework')],
            ] as $stat)
            <div class="text-white">
                <div class="text-3xl sm:text-4xl font-bold font-mono mb-1">{{ $stat['value'] }}</div>
                <div class="text-blue-200 text-sm">{{ $stat['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================== LATEST ARTICLES ===================== --}}
@php
    $latestArticles = \MiPressCz\Core\Models\Entry::query()
        ->with(['featuredImage'])
        ->whereHas('collection', fn ($q) => $q->where('handle', 'articles'))
        ->published()
        ->where('locale', app()->getLocale())
        ->orderByDesc('published_at')
        ->limit(3)
        ->get();
@endphp

@if($latestArticles->isNotEmpty())
<section class="bg-white dark:bg-gray-950 py-24 sm:py-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-12">
            <div>
                <span class="text-blue-600 dark:text-blue-400 text-sm font-semibold font-mono uppercase tracking-widest">{{ __('Blog') }}</span>
                <h2 class="mt-3 text-4xl sm:text-5xl font-bold text-gray-900 dark:text-white leading-tight">{{ __('Nejnovější příspěvky') }}</h2>
            </div>
            @php
                $articlesCollection = \MiPressCz\Core\Models\Collection::where('handle', 'articles')->first();
            @endphp
            @if($articlesCollection)
            <a href="{{ url('/articles') }}" class="hidden sm:inline-flex items-center gap-1 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                {{ __('Všechny příspěvky') }}
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
            </a>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($latestArticles as $article)
            <article class="group flex flex-col bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden hover:border-blue-300 dark:hover:border-blue-700 transition-all duration-200">
                @if($article->featured_image_id ?? null)
                <div class="aspect-video overflow-hidden bg-gray-200 dark:bg-gray-800">
                    <x-curator-glider
                        :media="$article->featured_image_id"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        width="600"
                        height="338"
                    />
                </div>
                @else
                <div class="aspect-video bg-gradient-to-br from-blue-100 dark:from-blue-950 to-blue-50 dark:to-gray-900 flex items-center justify-center">
                    <span class="text-4xl opacity-30">📄</span>
                </div>
                @endif

                <div class="flex flex-col flex-1 p-6">
                    @if($article->published_at)
                    <time class="text-xs text-gray-500 dark:text-gray-500 mb-3 font-mono">
                        {{ $article->published_at->translatedFormat('j. F Y') }}
                    </time>
                    @endif
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 leading-snug group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2">
                        <a href="{{ url($article->uri) }}">{{ $article->title }}</a>
                    </h3>
                    @if($article->meta_description ?? null)
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed flex-1 line-clamp-3">{{ $article->meta_description }}</p>
                    @endif
                    <a href="{{ url($article->uri) }}"
                       class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                        {{ __('Číst dál') }}
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </a>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===================== CTA ===================== --}}
<section class="relative overflow-hidden bg-gradient-to-br from-blue-600 to-blue-800 py-24 sm:py-32">
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 70% 50%, white 0%, transparent 70%)"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl sm:text-5xl font-bold text-white leading-tight mb-6">{{ __('Připraveni začít?') }}</h2>
        <p class="text-lg text-blue-200 max-w-2xl mx-auto mb-10">
            {{ __('Stáhněte si miPress, nainstalujte a spusťte svůj web ještě dnes. Otevřený zdrojový kód, žádné licenční poplatky.') }}
        </p>
        <div class="flex flex-wrap items-center justify-center gap-4">
            <a href="{{ url('/mpcp') }}"
               class="inline-flex items-center gap-2 bg-white text-blue-700 hover:bg-blue-50 font-semibold px-8 py-4 rounded-xl transition-colors shadow-lg">
                {{ __('Otevřít administraci') }}
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
            </a>
            <a href="https://github.com" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 border-2 border-white/40 hover:border-white text-white font-semibold px-8 py-4 rounded-xl transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .5C5.65.5.5 5.65.5 12c0 5.1 3.3 9.42 7.88 10.95.58.1.79-.25.79-.56v-2c-3.2.69-3.87-1.54-3.87-1.54-.53-1.34-1.3-1.7-1.3-1.7-1.06-.72.08-.71.08-.71 1.17.08 1.79 1.2 1.79 1.2 1.04 1.78 2.72 1.27 3.38.97.1-.75.41-1.27.74-1.56-2.55-.29-5.24-1.28-5.24-5.7 0-1.26.45-2.29 1.18-3.1-.12-.29-.51-1.47.11-3.06 0 0 .97-.31 3.17 1.18a11.05 11.05 0 0 1 5.78 0c2.2-1.49 3.17-1.18 3.17-1.18.62 1.59.23 2.77.11 3.06.74.81 1.18 1.84 1.18 3.1 0 4.43-2.7 5.41-5.27 5.69.42.36.79 1.07.79 2.16v3.2c0 .31.21.67.8.56A11.512 11.512 0 0 0 23.5 12C23.5 5.65 18.35.5 12 .5z"/></svg>
                GitHub
            </a>
        </div>
    </div>
</section>

@endif
@endsection

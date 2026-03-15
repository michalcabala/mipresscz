<x-filament-panels::page>
    @php
        $templates = $this->getTemplates();
        $activeTemplate = $this->getActiveTemplate();
        $activeTemplateMeta = $templates->firstWhere('slug', $activeTemplate);

        $palette = [
            'primary' => ['gradient' => 'from-primary-500 to-indigo-500', 'bg' => 'bg-primary-50 dark:bg-primary-500/10', 'text' => 'text-primary-600 dark:text-primary-400', 'ring' => 'ring-primary-200/80 dark:ring-primary-500/20', 'dot' => 'bg-primary-500'],
            'violet'  => ['gradient' => 'from-violet-500 to-purple-500',  'bg' => 'bg-violet-50 dark:bg-violet-500/10',   'text' => 'text-violet-600 dark:text-violet-400',   'ring' => 'ring-violet-200/80 dark:ring-violet-500/20',  'dot' => 'bg-violet-500'],
            'amber'   => ['gradient' => 'from-amber-500 to-orange-500',   'bg' => 'bg-amber-50 dark:bg-amber-500/10',     'text' => 'text-amber-600 dark:text-amber-400',     'ring' => 'ring-amber-200/80 dark:ring-amber-500/20',    'dot' => 'bg-amber-500'],
            'emerald' => ['gradient' => 'from-emerald-500 to-teal-500',   'bg' => 'bg-emerald-50 dark:bg-emerald-500/10',  'text' => 'text-emerald-600 dark:text-emerald-400',  'ring' => 'ring-emerald-200/80 dark:ring-emerald-500/20', 'dot' => 'bg-emerald-500'],
            'rose'    => ['gradient' => 'from-rose-500 to-pink-500',      'bg' => 'bg-rose-50 dark:bg-rose-500/10',        'text' => 'text-rose-600 dark:text-rose-400',        'ring' => 'ring-rose-200/80 dark:ring-rose-500/20',      'dot' => 'bg-rose-500'],
            'cyan'    => ['gradient' => 'from-cyan-500 to-sky-500',       'bg' => 'bg-cyan-50 dark:bg-cyan-500/10',        'text' => 'text-cyan-600 dark:text-cyan-400',        'ring' => 'ring-cyan-200/80 dark:ring-cyan-500/20',      'dot' => 'bg-cyan-500'],
        ];
        $paletteKeys = array_keys($palette);
    @endphp

    @if($templates->isEmpty())
        {{-- Empty state --}}
        <div class="flex flex-col items-center justify-center rounded-xl bg-white py-20 text-center ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-gray-200 to-gray-300 shadow-sm dark:from-gray-600 dark:to-gray-700">
                <x-filament::icon icon="heroicon-o-paint-brush" class="h-7 w-7 text-white"/>
            </div>
            <p class="mt-5 text-sm font-semibold text-gray-950 dark:text-white">{{ __('templates.no_templates') }}</p>
            <p class="mt-1.5 max-w-xs text-sm leading-relaxed text-gray-500 dark:text-gray-400">{{ __('templates.no_templates_hint') }}</p>
        </div>
    @else
        <div class="space-y-6">
            {{-- Header row --}}
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 shadow-lg shadow-primary-500/20 dark:shadow-primary-500/10">
                    <x-filament::icon icon="heroicon-o-paint-brush" class="h-6 w-6 text-white"/>
                </div>
                <div class="min-w-0">
                    <h2 class="text-lg font-medium tracking-tight text-gray-950 dark:text-white">
                        {{ $activeTemplateMeta['name'] ?? ucfirst($activeTemplate) }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('templates.header_subtitle') }}
                    </p>
                </div>
                <div class="ms-auto hidden items-center gap-1.5 rounded-full bg-primary-50 px-3 py-1 dark:bg-primary-500/10 sm:flex">
                    <div class="h-1.5 w-1.5 animate-pulse rounded-full bg-primary-500"></div>
                    <span class="text-xs font-semibold text-primary-700 dark:text-primary-400">
                        {{ trans_choice('templates.template_count', $templates->count(), ['count' => $templates->count()]) }}
                    </span>
                </div>
            </div>

            {{-- Template cards --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                @foreach($templates as $index => $template)
                    @php
                        $isActive = $activeTemplate === ($template['slug'] ?? '');
                        $stats = $this->getTemplateStats($template['path'] ?? '');
                        $c = $palette[$paletteKeys[$index % count($paletteKeys)]];
                    @endphp

                    <div @class([
                        'group overflow-hidden rounded-xl bg-white shadow-sm ring-1 transition-all dark:bg-gray-900',
                        'ring-primary-500/50 shadow-primary-500/5 dark:ring-primary-400/50' => $isActive,
                        'ring-gray-950/5 hover:shadow-md dark:ring-white/10 dark:hover:ring-white/20' => !$isActive,
                    ])>
                        {{-- Gradient accent bar --}}
                        <div class="h-1 bg-gradient-to-r {{ $isActive ? 'from-primary-500 to-indigo-500' : $c['gradient'] }}"></div>

                        {{-- Screenshot / Visual header --}}
                        <div class="relative">
                            @if($template['screenshot_exists'] ?? false)
                                <div class="h-40 overflow-hidden">
                                    <img
                                        src="{{ asset('templates/'.($template['slug'] ?? '').'/'.($template['screenshot'] ?? 'screenshot.png')) }}"
                                        alt="{{ $template['name'] ?? '' }}"
                                        class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.03]"
                                    >
                                </div>
                            @else
                                <div class="flex h-32 items-center justify-center bg-gray-50/80 dark:bg-white/[0.02]">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-xl ring-1 {{ $c['bg'] }} {{ $c['ring'] }}">
                                        <x-filament::icon icon="heroicon-o-paint-brush" class="h-6 w-6 {{ $c['text'] }}"/>
                                    </div>
                                </div>
                            @endif

                            {{-- Active pill --}}
                            @if($isActive)
                                <div class="absolute right-3 top-3">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-primary-600 py-1 pl-1.5 pr-2.5 text-[11px] font-semibold text-white shadow-lg shadow-primary-600/30">
                                        <x-filament::icon icon="heroicon-m-check" class="h-3.5 w-3.5"/>
                                        {{ __('templates.active') }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Card body --}}
                        <div class="flex flex-col p-5">
                            {{-- Name row --}}
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg ring-1 {{ $c['bg'] }} {{ $c['ring'] }}">
                                    <x-filament::icon icon="heroicon-o-swatch" class="h-5 w-5 {{ $c['text'] }}"/>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-sm font-semibold text-gray-950 dark:text-white">
                                        {{ $template['name'] ?? $template['slug'] ?? '—' }}
                                    </h3>
                                    <p class="mt-0.5 font-mono text-xs text-gray-400 dark:text-gray-500">
                                        {{ $template['slug'] ?? '—' }}
                                    </p>
                                </div>
                            </div>

                            {{-- Description --}}
                            @if($template['description'] ?? null)
                                <p class="mt-3 line-clamp-2 text-[13px] leading-relaxed text-gray-500 dark:text-gray-400">
                                    {{ $template['description'] }}
                                </p>
                            @endif

                            {{-- Metadata --}}
                            <div class="mt-4 border-t border-gray-100 pt-3 dark:border-white/5">
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-gray-500 dark:text-gray-400">
                                    @if($template['version'] ?? null)
                                        <span class="inline-flex items-center gap-1">
                                            <x-filament::icon icon="heroicon-m-tag" class="h-3.5 w-3.5 text-gray-400 dark:text-gray-500"/>
                                            v{{ $template['version'] }}
                                        </span>
                                    @endif
                                    @if($template['author'] ?? null)
                                        <span class="inline-flex items-center gap-1">
                                            <x-filament::icon icon="heroicon-m-user" class="h-3.5 w-3.5 text-gray-400 dark:text-gray-500"/>
                                            {{ $template['author'] }}
                                        </span>
                                    @endif
                                    @if(($template['slug'] ?? null) === 'default')
                                        <span class="inline-flex items-center gap-1 font-semibold text-success-600 dark:text-success-400">
                                            <div class="h-1.5 w-1.5 rounded-full bg-success-500"></div>
                                            {{ __('templates.default_badge') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Template structure --}}
                            @if($stats['total'] > 0)
                                <div class="mt-3 flex flex-wrap gap-x-3 gap-y-1 text-xs text-gray-400 dark:text-gray-500">
                                    @foreach(['layouts', 'pages', 'partials', 'errors'] as $type)
                                        @if($stats[$type] > 0)
                                            <span>
                                                <span class="font-medium text-gray-600 dark:text-gray-300">{{ $stats[$type] }}</span>
                                                {{ __("templates.stats_{$type}") }}
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            {{-- Action --}}
                            <div class="mt-4">
                                @if($isActive)
                                    <div class="flex items-center justify-center gap-1.5 rounded-lg bg-primary-50 py-2 text-xs font-semibold text-primary-700 dark:bg-primary-500/10 dark:text-primary-400">
                                        <x-filament::icon icon="heroicon-m-check-circle" class="h-4 w-4"/>
                                        {{ __('templates.currently_active') }}
                                    </div>
                                @else
                                    <button
                                        wire:click="activate('{{ e($template['slug'] ?? '') }}')"
                                        wire:loading.attr="disabled"
                                        wire:target="activate('{{ e($template['slug'] ?? '') }}')"
                                        type="button"
                                        class="flex w-full items-center justify-center gap-1.5 rounded-lg bg-gray-900 px-4 py-2 text-xs font-semibold text-white shadow-sm transition-all duration-150 hover:bg-gray-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100"
                                    >
                                        <span wire:loading.remove wire:target="activate('{{ e($template['slug'] ?? '') }}')">
                                            {{ __('templates.activate') }}
                                        </span>
                                        <span wire:loading wire:target="activate('{{ e($template['slug'] ?? '') }}')">
                                            <x-filament::loading-indicator class="h-4 w-4"/>
                                        </span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</x-filament-panels::page>

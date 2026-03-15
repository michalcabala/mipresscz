<x-filament-panels::page>
    @php
        $templates = $this->getTemplates();
        $activeTemplate = $this->getActiveTemplate();
    @endphp

    @if($templates->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-xl bg-white py-20 text-center ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-gray-100 dark:bg-white/10">
                <x-filament::icon icon="heroicon-o-paint-brush" class="h-7 w-7 text-gray-400"/>
            </div>
            <p class="mt-4 text-sm font-semibold text-gray-950 dark:text-white">{{ __('templates.no_templates') }}</p>
            <p class="mt-1.5 max-w-xs text-sm text-gray-500 dark:text-gray-400">{{ __('templates.no_templates_hint') }}</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
            @foreach($templates as $template)
                @php
                    $isActive = $activeTemplate === ($template['slug'] ?? '');
                    $stats    = $this->getTemplateStats($template['path'] ?? '');
                    $slug     = $template['slug'] ?? '';
                    $name     = $template['name'] ?? ucfirst($slug) ?: '—';
                @endphp

                <div @class([
                    'group flex flex-col overflow-hidden rounded-xl bg-white ring-1 transition-all dark:bg-gray-900',
                    'ring-primary-500 shadow-lg' => $isActive,
                    'ring-gray-950/5 hover:shadow-md dark:ring-white/10' => !$isActive,
                ])>

                    {{-- Visual header --}}
                    <div @class([
                        'relative flex h-32 shrink-0 items-center justify-center',
                        'bg-primary-50 dark:bg-primary-950' => $isActive,
                        'bg-gray-50 dark:bg-gray-800' => !$isActive,
                    ])>
                        @if($template['screenshot_exists'] ?? false)
                            <img
                                src="{{ asset('templates/'.$slug.'/'.($template['screenshot'] ?? 'screenshot.png')) }}"
                                alt="{{ $name }}"
                                class="h-full w-full object-cover"
                            >
                        @else
                            <div class="flex flex-col items-center gap-2">
                                <div @class([
                                    'flex h-12 w-12 items-center justify-center rounded-xl',
                                    'bg-primary-100 dark:bg-primary-500/20' => $isActive,
                                    'bg-white ring-1 ring-gray-200 dark:bg-gray-700 dark:ring-white/10' => !$isActive,
                                ])>
                                    <x-filament::icon
                                        icon="heroicon-o-swatch"
                                        @class([
                                            'h-6 w-6',
                                            'text-primary-500 dark:text-primary-400' => $isActive,
                                            'text-gray-400 dark:text-gray-500' => !$isActive,
                                        ])
                                    />
                                </div>
                                <span class="text-xs font-medium text-gray-400 dark:text-gray-500">
                                    {{ __('templates.no_preview') }}
                                </span>
                            </div>
                        @endif

                        {{-- Active badge --}}
                        @if($isActive)
                            <span class="absolute right-2.5 top-2.5 inline-flex items-center gap-1 rounded-full bg-primary-600 py-0.5 pl-1.5 pr-2 text-xs font-semibold text-white shadow">
                                <x-filament::icon icon="heroicon-m-check" class="h-3 w-3"/>
                                {{ __('templates.active') }}
                            </span>
                        @endif
                    </div>

                    {{-- Body --}}
                    <div class="flex flex-1 flex-col gap-y-3 p-4">

                        {{-- Name + default badge --}}
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-gray-950 dark:text-white">{{ $name }}</p>
                                <p class="mt-0.5 font-mono text-xs text-gray-400 dark:text-gray-500">{{ $slug }}</p>
                            </div>
                            @if($slug === 'default')
                                <span class="mt-0.5 shrink-0 rounded-md bg-success-50 px-1.5 py-0.5 text-xs font-semibold text-success-700 ring-1 ring-success-600/20 dark:bg-success-500/10 dark:text-success-400">
                                    {{ __('templates.default_badge') }}
                                </span>
                            @endif
                        </div>

                        {{-- Description --}}
                        @if($template['description'] ?? null)
                            <p class="text-sm leading-relaxed text-gray-500 line-clamp-2 dark:text-gray-400">
                                {{ $template['description'] }}
                            </p>
                        @endif

                        {{-- Version + author --}}
                        @if(($template['version'] ?? null) || ($template['author'] ?? null))
                            <div class="flex items-center gap-3 text-xs text-gray-400 dark:text-gray-500">
                                @if($template['version'] ?? null)
                                    <span class="flex items-center gap-1">
                                        <x-filament::icon icon="heroicon-m-tag" class="h-3.5 w-3.5"/>
                                        v{{ $template['version'] }}
                                    </span>
                                @endif
                                @if($template['author'] ?? null)
                                    <span class="flex items-center gap-1">
                                        <x-filament::icon icon="heroicon-m-user" class="h-3.5 w-3.5"/>
                                        {{ $template['author'] }}
                                    </span>
                                @endif
                            </div>
                        @endif

                        {{-- File stats --}}
                        @if($stats['total'] > 0)
                            <div class="flex flex-wrap gap-x-3 gap-y-1 border-t border-gray-100 pt-2.5 text-xs text-gray-400 dark:border-white/5 dark:text-gray-500">
                                @foreach(['layouts', 'pages', 'partials', 'errors'] as $type)
                                    @if($stats[$type] > 0)
                                        <span>
                                            <span class="font-semibold text-gray-600 dark:text-gray-300">{{ $stats[$type] }}</span>&thinsp;{{ __("templates.stats_{$type}") }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        {{-- Action --}}
                        <div class="mt-auto pt-1">
                            @if($isActive)
                                <div class="flex items-center justify-center gap-1.5 rounded-lg bg-primary-50 py-2.5 text-xs font-semibold text-primary-700 dark:bg-primary-500/10 dark:text-primary-400">
                                    <x-filament::icon icon="heroicon-m-check-circle" class="h-4 w-4"/>
                                    {{ __('templates.currently_active') }}
                                </div>
                            @else
                                <button
                                    wire:click="activate('{{ e($slug) }}')"
                                    wire:loading.attr="disabled"
                                    wire:target="activate('{{ e($slug) }}')"
                                    type="button"
                                    class="flex w-full items-center justify-center gap-1.5 rounded-lg bg-gray-900 px-4 py-2.5 text-xs font-semibold text-white transition-colors hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100"
                                >
                                    <span wire:loading.remove wire:target="activate('{{ e($slug) }}')">
                                        {{ __('templates.activate') }}
                                    </span>
                                    <span wire:loading wire:target="activate('{{ e($slug) }}')">
                                        <x-filament::loading-indicator class="h-4 w-4"/>
                                    </span>
                                </button>
                            @endif
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>

<x-filament-panels::page>
    @php
        $templates = $this->getTemplates();
        $activeTemplate = $this->getActiveTemplate();
        $activeTemplateMeta = $templates->firstWhere('slug', $activeTemplate);
    @endphp

    @if($templates->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-xl bg-white py-16 text-center ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                <x-filament::icon icon="heroicon-o-paint-brush" class="h-7 w-7 text-gray-400 dark:text-gray-500"/>
            </div>
            <p class="mt-4 text-sm font-semibold text-gray-950 dark:text-white">{{ __('templates.no_templates') }}</p>
            <p class="mt-1 max-w-sm text-sm text-gray-500 dark:text-gray-400">{{ __('templates.no_templates_hint') }}</p>
        </div>
    @else
        {{-- Active template info banner --}}
        <div class="flex flex-wrap items-center gap-x-6 gap-y-3 rounded-xl bg-white p-4 ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-50 dark:bg-primary-500/10">
                    <x-filament::icon icon="heroicon-m-check-circle" class="h-5 w-5 text-primary-600 dark:text-primary-400"/>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('templates.active_template_label') }}</p>
                    <p class="truncate text-sm font-semibold text-gray-950 dark:text-white">
                        {{ $activeTemplateMeta['name'] ?? ucfirst($activeTemplate) }}
                        <span class="font-normal text-gray-400 dark:text-gray-500">&middot; {{ $activeTemplate }}</span>
                    </p>
                </div>
            </div>

            <div class="hidden h-8 w-px bg-gray-200 sm:block dark:bg-white/10"></div>

            <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                @if($activeTemplateMeta['version'] ?? null)
                    <span class="inline-flex items-center gap-1">
                        <x-filament::icon icon="heroicon-m-tag" class="h-3.5 w-3.5"/>
                        v{{ $activeTemplateMeta['version'] }}
                    </span>
                @endif
                @if($activeTemplateMeta['author'] ?? null)
                    <span class="inline-flex items-center gap-1">
                        <x-filament::icon icon="heroicon-m-user" class="h-3.5 w-3.5"/>
                        {{ $activeTemplateMeta['author'] }}
                    </span>
                @endif
                <span class="inline-flex items-center gap-1">
                    <x-filament::icon icon="heroicon-m-square-3-stack-3d" class="h-3.5 w-3.5"/>
                    {{ trans_choice('templates.template_count', $templates->count(), ['count' => $templates->count()]) }}
                </span>
            </div>
        </div>

        {{-- Template grid --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
            @foreach($templates as $template)
                @php
                    $isActive = $activeTemplate === ($template['slug'] ?? '');
                    $stats = $this->getTemplateStats($template['path'] ?? '');
                    $initial = mb_strtoupper(mb_substr($template['name'] ?? $template['slug'] ?? 'T', 0, 1));
                @endphp

                <div @class([
                    'group flex flex-col overflow-hidden rounded-xl bg-white shadow-sm ring-1 transition-all dark:bg-white/5',
                    'ring-2 ring-primary-500 dark:ring-primary-400' => $isActive,
                    'ring-gray-950/5 hover:shadow-md dark:ring-white/10 dark:hover:ring-white/20' => !$isActive,
                ])>
                    {{-- Screenshot / Visual header --}}
                    <div class="relative h-44 overflow-hidden">
                        @if($template['screenshot_exists'] ?? false)
                            <img
                                src="{{ asset('templates/'.($template['slug'] ?? '').'/'.($template['screenshot'] ?? 'screenshot.png')) }}"
                                alt="{{ $template['name'] ?? '' }}"
                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                            >
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-50 via-gray-100 to-gray-200 dark:from-gray-800 dark:via-gray-750 dark:to-gray-700">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-2xl font-bold text-gray-400 shadow-sm ring-1 ring-gray-900/5 dark:bg-gray-600 dark:text-gray-300 dark:ring-white/10">
                                        {{ $initial }}
                                    </div>
                                    <span class="text-[11px] font-medium tracking-wide text-gray-400 dark:text-gray-500">
                                        {{ __('templates.no_preview') }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        {{-- Active badge overlay --}}
                        @if($isActive)
                            <div class="absolute right-3 top-3 flex items-center gap-1 rounded-full bg-primary-600 py-1 pl-1.5 pr-2.5 text-xs font-semibold text-white shadow-md">
                                <x-filament::icon icon="heroicon-m-check" class="h-3.5 w-3.5"/>
                                {{ __('templates.active') }}
                            </div>
                        @endif
                    </div>

                    {{-- Card body --}}
                    <div class="flex flex-1 flex-col p-5">
                        {{-- Name + slug --}}
                        <div class="mb-3">
                            <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                                {{ $template['name'] ?? $template['slug'] ?? '—' }}
                            </h3>
                            <p class="mt-0.5 font-mono text-xs text-gray-400 dark:text-gray-500">
                                {{ $template['slug'] ?? '—' }}
                            </p>
                        </div>

                        {{-- Description --}}
                        @if($template['description'] ?? null)
                            <p class="mb-4 line-clamp-3 text-sm leading-relaxed text-gray-600 dark:text-gray-400">
                                {{ $template['description'] }}
                            </p>
                        @endif

                        {{-- Metadata badges --}}
                        <div class="mb-4 flex flex-wrap items-center gap-1.5">
                            @if($template['version'] ?? null)
                                <span class="inline-flex items-center gap-1 rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-200 dark:bg-white/5 dark:text-gray-300 dark:ring-white/10">
                                    <x-filament::icon icon="heroicon-m-tag" class="h-3 w-3 text-gray-400 dark:text-gray-500"/>
                                    v{{ $template['version'] }}
                                </span>
                            @endif

                            @if($template['author'] ?? null)
                                <span class="inline-flex items-center gap-1 rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-200 dark:bg-white/5 dark:text-gray-300 dark:ring-white/10">
                                    <x-filament::icon icon="heroicon-m-user" class="h-3 w-3 text-gray-400 dark:text-gray-500"/>
                                    {{ $template['author'] }}
                                </span>
                            @endif

                            @if(($template['slug'] ?? null) === 'default')
                                <span class="inline-flex items-center gap-1 rounded-md bg-success-50 px-2 py-1 text-xs font-semibold text-success-700 ring-1 ring-inset ring-success-600/20 dark:bg-success-500/10 dark:text-success-400 dark:ring-success-500/30">
                                    {{ __('templates.default_badge') }}
                                </span>
                            @endif
                        </div>

                        {{-- Template structure stats --}}
                        @if($stats['total'] > 0)
                            <div class="mb-4 flex flex-wrap gap-x-4 gap-y-1.5 border-t border-gray-100 pt-3 dark:border-white/5">
                                @foreach(['layouts', 'pages', 'partials', 'errors'] as $type)
                                    @if($stats[$type] > 0)
                                        <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                            <x-filament::icon icon="heroicon-m-document-text" class="h-3.5 w-3.5 text-gray-400 dark:text-gray-500"/>
                                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $stats[$type] }}</span>
                                            {{ __("templates.stats_{$type}") }}
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        {{-- Spacer to push action to bottom --}}
                        <div class="flex-1"></div>

                        {{-- Action area --}}
                        @if($isActive)
                            <div class="flex items-center justify-center gap-2 rounded-lg border border-primary-200 bg-primary-50/50 py-2.5 text-sm font-medium text-primary-700 dark:border-primary-500/30 dark:bg-primary-500/10 dark:text-primary-400">
                                <x-filament::icon icon="heroicon-m-check-circle" class="h-4 w-4"/>
                                {{ __('templates.currently_active') }}
                            </div>
                        @else
                            <button
                                wire:click="activate('{{ e($template['slug'] ?? '') }}')"
                                wire:loading.attr="disabled"
                                wire:target="activate('{{ e($template['slug'] ?? '') }}')"
                                type="button"
                                class="flex w-full items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <span wire:loading.remove wire:target="activate('{{ e($template['slug'] ?? '') }}')">
                                    {{ __('templates.activate') }}
                                </span>
                                <span wire:loading wire:target="activate('{{ e($template['slug'] ?? '') }}')">
                                    <x-filament::loading-indicator class="h-5 w-5"/>
                                </span>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>

<x-filament-panels::page>

    @if($this->getTemplates()->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <x-filament::icon
                icon="heroicon-o-paint-brush"
                class="w-12 h-12 text-gray-300 mb-4"
            />
            <p class="text-sm font-medium text-gray-900">{{ __('templates.no_templates') }}</p>
            <p class="mt-1 text-sm text-gray-500">{{ __('templates.no_templates_hint') }}</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($this->getTemplates() as $template)
                @php $isActive = $this->getActiveTemplate() === ($template['slug'] ?? ''); @endphp

                <div @class([
                    'rounded-xl border-2 bg-white shadow-sm transition-all flex flex-col',
                    'border-primary-500 ring-2 ring-primary-500/10' => $isActive,
                    'border-gray-200 hover:border-gray-300' => !$isActive,
                ])>

                    {{-- Screenshot placeholder --}}
                    <div class="h-40 bg-gradient-to-br from-gray-100 to-gray-200 rounded-t-xl flex items-center justify-center overflow-hidden">
                        @if($template['screenshot_exists'] ?? false)
                            <img src="{{ asset('templates/'.($template['slug'] ?? '').'/'.($template['screenshot'] ?? 'screenshot.png')) }}"
                                 alt="{{ $template['name'] ?? '' }}"
                                 class="w-full h-full object-cover">
                        @else
                            <x-filament::icon
                                icon="heroicon-o-photo"
                                class="w-10 h-10 text-gray-300"
                            />
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex flex-col flex-1 p-5">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="font-semibold text-gray-900 text-sm">
                                {{ $template['name'] ?? $template['slug'] ?? '—' }}
                            </h3>

                            @if($isActive)
                                <span class="inline-flex items-center rounded-full bg-primary-50 px-2 py-0.5 text-xs font-medium text-primary-700 ring-1 ring-primary-200 shrink-0 ml-2">
                                    {{ __('templates.active') }}
                                </span>
                            @endif
                        </div>

                        <p class="text-xs text-gray-500 mb-1">
                            v{{ $template['version'] ?? '—' }}
                            @if($template['author'] ?? null)
                                &middot; {{ $template['author'] }}
                            @endif
                        </p>

                        @if($template['description'] ?? null)
                            <p class="text-sm text-gray-600 flex-1 mb-4">{{ $template['description'] }}</p>
                        @else
                            <div class="flex-1 mb-4"></div>
                        @endif

                        {{-- Action --}}
                        @if($isActive)
                            <div class="text-center text-xs font-medium text-primary-600 py-2 border border-primary-200 rounded-lg bg-primary-50">
                                {{ __('templates.currently_active') }}
                            </div>
                        @else
                            <button
                                wire:click="activate('{{ $template['slug'] ?? '' }}')"
                                wire:loading.attr="disabled"
                                wire:target="activate('{{ $template['slug'] ?? '' }}')"
                                type="button"
                                class="w-full rounded-lg bg-primary-600 px-3 py-2 text-sm font-medium text-white hover:bg-primary-700 transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
                            >
                                <span wire:loading.remove wire:target="activate('{{ $template['slug'] ?? '' }}')">
                                    {{ __('templates.activate') }}
                                </span>
                                <span wire:loading wire:target="activate('{{ $template['slug'] ?? '' }}')">
                                    …
                                </span>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</x-filament-panels::page>

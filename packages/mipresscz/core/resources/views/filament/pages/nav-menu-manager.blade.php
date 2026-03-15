{{-- ============================================================
     Nav Menu Manager Page
============================================================ --}}

<x-filament-panels::page>
    <x-filament::section>

        {{-- Location bar --}}
        <div class="fmm-location-bar">
            @foreach($locations as $handle => $label)
                <button
                    class="fmm-location-btn {{ $activeLocation === $handle ? 'active' : '' }}"
                    wire:click="switchLocation('{{ $handle }}')"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Menu switcher --}}
        @if($activeLocation)
            <div class="fmm-location-bar" style="margin-bottom:0.5rem">
                @foreach($menusForActiveLocation as $menu)
                    <button
                        class="fmm-menu-btn {{ $activeMenuId === $menu->id ? 'active' : '' }}"
                        wire:click="switchMenu({{ $menu->id }})"
                    >
                        {{ $menu->name }}
                    </button>
                @endforeach
            </div>
        @endif

        {{-- Main builder layout --}}
        <div class="fmm-manager">
            @livewire('nav-menu-builder', [
                'menuId' => $activeMenuId,
                'locationHandle' => $activeLocation ?? '',
            ], key('builder-'.($activeMenuId ?? 'none')))

            @livewire('nav-menu-panel', [
                'menuId' => $activeMenuId,
                'locationHandle' => $activeLocation ?? '',
            ], key('panel-'.($activeMenuId ?? 'none')))
        </div>

        {{-- Auto-save flash --}}
        <div id="fmm-autosave-flash">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:1rem;height:1rem">
                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
            </svg>
            {{ __('content.menus.saved') }}
        </div>

    </x-filament::section>
</x-filament-panels::page>

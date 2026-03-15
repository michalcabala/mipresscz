<footer class="bg-gray-950 text-gray-400 mt-auto border-t border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-12">

            {{-- Brand --}}
            <div class="sm:col-span-2 lg:col-span-1">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 group mb-4">
                    <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-blue-600 text-white text-sm font-bold font-mono group-hover:bg-blue-500 transition-colors">m/</span>
                    <span class="font-semibold text-white text-lg">{{ config('app.name', 'miPress') }}</span>
                </a>
                <p class="text-sm leading-relaxed max-w-xs">{{ __('Moderní CMS na Laravel 12 a Filament 5. Flexibilní, rychlé, přizpůsobitelné.') }}</p>
            </div>

            {{-- Navigation --}}
            <div>
                <h3 class="text-white text-sm font-semibold mb-4 uppercase tracking-wider">{{ __('Navigace') }}</h3>
                <ul class="space-y-3">
                    <li><a href="{{ url('/') }}" class="text-sm hover:text-white transition-colors">{{ __('Domů') }}</a></li>
                    @if(!empty($footerMenu))
                        <li>
                            @include('mipresscz-core::components.nav-menu-list', [
                                'items' => $footerMenu,
                                'class' => 'space-y-3',
                                'childClass' => 'mt-2 space-y-2 border-l border-gray-800 pl-4',
                                'itemClass' => 'text-sm hover:text-white transition-colors',
                            ])
                        </li>
                    @else
                        @foreach($footerEntries ?? [] as $entry)
                            <li><a href="{{ url($entry->uri) }}" class="text-sm hover:text-white transition-colors">{{ $entry->title }}</a></li>
                        @endforeach
                    @endif
                </ul>
            </div>

            {{-- Resources --}}
            <div>
                <h3 class="text-white text-sm font-semibold mb-4 uppercase tracking-wider">{{ __('Zdroje') }}</h3>
                <ul class="space-y-3">
                    <li><a href="{{ url('/mpcp') }}" class="text-sm hover:text-white transition-colors">{{ __('Administrace') }}</a></li>
                    <li><a href="https://laravel.com" target="_blank" rel="noopener" class="text-sm hover:text-white transition-colors">Laravel</a></li>
                    <li><a href="https://filamentphp.com" target="_blank" rel="noopener" class="text-sm hover:text-white transition-colors">Filament</a></li>
                    <li><a href="https://tailwindcss.com" target="_blank" rel="noopener" class="text-sm hover:text-white transition-colors">Tailwind CSS</a></li>
                </ul>
            </div>

            {{-- Social / Stack --}}
            <div>
                <h3 class="text-white text-sm font-semibold mb-4 uppercase tracking-wider">{{ __('Stack') }}</h3>
                <ul class="space-y-3">
                    <li class="flex items-center gap-2 text-sm"><span class="text-blue-400 font-mono text-xs">//</span> PHP 8.3.4</li>
                    <li class="flex items-center gap-2 text-sm"><span class="text-blue-400 font-mono text-xs">//</span> Laravel 12</li>
                    <li class="flex items-center gap-2 text-sm"><span class="text-blue-400 font-mono text-xs">//</span> Filament 5</li>
                    <li class="flex items-center gap-2 text-sm"><span class="text-blue-400 font-mono text-xs">//</span> Tailwind CSS 4</li>
                </ul>
            </div>
        </div>

        <div class="mt-10 pt-8 border-t border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-gray-600">
                &copy; {{ date('Y') }} {{ config('app.name', 'miPress') }}. {{ __('Všechna práva vyhrazena.') }}
            </p>
            <p class="text-xs text-gray-700">
                {{ __('Postaveno s') }} <span class="text-blue-500">miPress</span>
            </p>
        </div>
    </div>
</footer>

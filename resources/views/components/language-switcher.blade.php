@if($items->count() > 1)
    <nav aria-label="{{ __('locales.language_switcher') }}" class="language-switcher">
        <ul class="flex items-center gap-2">
            @foreach($items as $item)
                @php
                    /** @var \App\Models\Locale $locale */
                    $locale = $item['locale'];
                @endphp
                <li>
                    @if($item['is_current'])
                        <span
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-800 font-medium text-sm"
                            aria-current="true"
                        >
                            @if($locale->flag)
                                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full overflow-hidden shrink-0">
                                    <img
                                        src="{{ asset('assets/flags/'.$locale->flag) }}"
                                        alt="{{ $locale->native_name }}"
                                        class="w-full h-full object-cover"
                                        loading="lazy"
                                    />
                                </span>
                            @endif
                            <span>{{ $locale->native_name }}</span>
                        </span>
                    @else
                        <a
                            href="{{ $item['url'] }}"
                            hreflang="{{ $locale->code }}"
                            lang="{{ $locale->code }}"
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                        >
                            @if($locale->flag)
                                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full overflow-hidden shrink-0">
                                    <img
                                        src="{{ asset('assets/flags/'.$locale->flag) }}"
                                        alt="{{ $locale->native_name }}"
                                        class="w-full h-full object-cover"
                                        loading="lazy"
                                    />
                                </span>
                            @endif
                            <span>{{ $locale->native_name }}</span>
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>
    </nav>
@endif

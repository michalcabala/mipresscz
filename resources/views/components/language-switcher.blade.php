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
                            class="language-switcher__item language-switcher__item--active"
                            aria-current="true"
                        >
                            @if($locale->flag)
                                <img
                                    src="{{ asset('assets/flags/'.$locale->flag) }}"
                                    alt="{{ $locale->native_name }}"
                                    width="20"
                                    height="15"
                                    loading="lazy"
                                />
                            @endif
                            <span>{{ $locale->native_name }}</span>
                        </span>
                    @else
                        <a
                            href="{{ $item['url'] }}"
                            hreflang="{{ $locale->code }}"
                            lang="{{ $locale->code }}"
                            class="language-switcher__item"
                        >
                            @if($locale->flag)
                                <img
                                    src="{{ asset('assets/flags/'.$locale->flag) }}"
                                    alt="{{ $locale->native_name }}"
                                    width="20"
                                    height="15"
                                    loading="lazy"
                                />
                            @endif
                            <span>{{ $locale->native_name }}</span>
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>
    </nav>
@endif

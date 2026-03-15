{{--
    Nav Menu Component
    Renders a nested navigation menu from the menu manager.

    Usage:
        @include('mipresscz-core::components.nav-menu', ['items' => $primaryMenu])
        @include('mipresscz-core::components.nav-menu', ['items' => $primaryMenu, 'class' => 'gap-1', 'itemClass' => 'px-3 py-2'])
        @include('mipresscz-core::components.nav-menu', ['items' => menu('primary')])

    Props:
        $items     — array of menu nodes (from NavComposer or menu() helper)
        $class     — CSS classes for the <nav> wrapper (optional)
        $itemClass — CSS classes for each <a> link (optional)
        $depth     — current nesting depth (internal, do not set manually)
--}}

@php
    $items = $items ?? [];
    $depth = $depth ?? 0;
    $class = $class ?? '';
    $itemClass = $itemClass ?? '';
@endphp

@if(count($items))
    @if($depth === 0)
        <nav @if($class) class="{{ $class }}" @endif>
    @endif

    <ul @class([
        'flex items-center' => $depth === 0,
        'absolute left-0 top-full mt-1 min-w-48 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900 py-1 z-50 hidden group-hover:block' => $depth > 0,
    ])>
        @foreach($items as $item)
            @php
                $hasChildren = !empty($item['children']);
                $isActive = request()->url() === $item['url'] || (
                    $item['url'] !== url('/') && str_starts_with(request()->url(), $item['url'])
                );
            @endphp

            <li @class(['relative group' => $hasChildren, 'relative' => !$hasChildren])>
                <a
                    href="{{ $item['url'] }}"
                    @if($item['target'] !== '_self') target="{{ $item['target'] }}" rel="noopener" @endif
                    @class([
                        $itemClass,
                        'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/50' => $isActive && $depth === 0,
                        'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800' => !$isActive && $depth === 0,
                        'block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' => $depth > 0,
                        'font-medium text-blue-600 dark:text-blue-400' => $isActive && $depth > 0,
                    ])
                >
                    {{ $item['title'] }}
                    @if($hasChildren && $depth === 0)
                        <svg class="inline-block w-3 h-3 ml-0.5 opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    @endif
                </a>

                @if($hasChildren)
                    @include('mipresscz-core::components.nav-menu', [
                        'items' => $item['children'],
                        'depth' => $depth + 1,
                        'class' => $class,
                        'itemClass' => $itemClass,
                    ])
                @endif
            </li>
        @endforeach
    </ul>

    @if($depth === 0)
        </nav>
    @endif
@endif

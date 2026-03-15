{{--
    Nav Menu List Component
    Renders a flat list of menu items (no dropdowns). Useful for footers and mobile menus.

    Usage:
        @include('mipresscz-core::components.nav-menu-list', ['items' => $footerMenu])
        @include('mipresscz-core::components.nav-menu-list', ['items' => $footerMenu, 'class' => 'space-y-3', 'itemClass' => 'text-sm'])

    Props:
        $items     — array of menu nodes (from NavComposer or menu() helper)
        $class     — CSS classes for the <ul> wrapper (optional)
        $itemClass — CSS classes for each <a> link (optional)
--}}

@php
    $items = $items ?? [];
    $class = $class ?? 'space-y-3';
    $itemClass = $itemClass ?? 'text-sm hover:text-white transition-colors';
    $depth = $depth ?? 0;
    $childClass = $childClass ?? 'mt-2 space-y-2 border-l border-gray-200 pl-4 dark:border-gray-700';
    $itemOnclick = $itemOnclick ?? null;
@endphp

@if(count($items))
    <ul @if($class) class="{{ $class }}" @endif>
        @foreach($items as $item)
            <li>
                <a
                    href="{{ $item['url'] }}"
                    @if(($item['target'] ?? '_self') !== '_self') target="{{ $item['target'] }}" rel="noopener" @endif
                    @if($itemOnclick) onclick="{{ $itemOnclick }}" @endif
                    class="{{ $itemClass }}"
                >{{ $item['title'] }}</a>

                @if(!empty($item['children']))
                    @include('mipresscz-core::components.nav-menu-list', [
                        'items' => $item['children'],
                        'class' => $childClass,
                        'itemClass' => $itemClass,
                        'depth' => $depth + 1,
                        'childClass' => $childClass,
                        'itemOnclick' => $itemOnclick,
                    ])
                @endif
            </li>
        @endforeach
    </ul>
@endif

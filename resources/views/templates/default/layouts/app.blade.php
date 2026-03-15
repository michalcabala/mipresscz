<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index, follow">

    <title>@yield('title', config('app.name', 'miPress'))</title>

    @if(strlen(trim((string) View::yieldContent('description'))))
    <meta name="description" content="@yield('description')">
    @endif

    @isset($canonicalUrl)
    <link rel="canonical" href="{{ $canonicalUrl }}">
    @endisset

    @if(isset($hreflangLinks) && $hreflangLinks->count() > 1)
    @foreach($hreflangLinks as $locale => $link)
    <link rel="alternate" hreflang="{{ $locale }}" href="{{ $link['url'] }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $hreflangLinks->first()['url'] }}">
    @endif

    @yield('meta')

    {{-- Frontend theme bootstrap --}}
    <script>
        function miPressResolveTheme() {
            try {
                var savedTheme = localStorage.getItem('theme') || localStorage.getItem('mipress-theme');

                if (savedTheme === 'dark' || savedTheme === 'light') {
                    return savedTheme;
                }
            } catch (error) {
                // no-op
            }

            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        function miPressApplyTheme(theme) {
            var resolvedTheme = theme === 'dark' ? 'dark' : 'light';
            var root = document.documentElement;

            root.classList.toggle('dark', resolvedTheme === 'dark');
            root.style.colorScheme = resolvedTheme;

            try {
                localStorage.setItem('theme', resolvedTheme);
                localStorage.removeItem('mipress-theme');
            } catch (error) {
                // no-op
            }

            return resolvedTheme;
        }

        miPressApplyTheme(miPressResolveTheme());
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('head')
</head>
<body class="flex flex-col min-h-full bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 antialiased">

    @include('template::partials.header')

    <main class="flex-1">
        @yield('content')
    </main>

    @include('template::partials.footer')

    <script>
        function miPressToggleTheme() {
            miPressApplyTheme(document.documentElement.classList.contains('dark') ? 'light' : 'dark');
        }

        function miPressOpenMenu() {
            var overlay = document.getElementById('mobile-nav-overlay');
            if (overlay) {
                overlay.classList.remove('hidden');
                requestAnimationFrame(function () {
                    overlay.classList.add('mp-menu-open');
                });
                document.body.style.overflow = 'hidden';
            }
        }

        function miPressCloseMenu() {
            var overlay = document.getElementById('mobile-nav-overlay');
            if (overlay) {
                overlay.classList.remove('mp-menu-open');
                setTimeout(function () { overlay.classList.add('hidden'); }, 300);
                document.body.style.overflow = '';
            }
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { miPressCloseMenu(); }
        });

        document.addEventListener('DOMContentLoaded', function () {
            miPressApplyTheme(miPressResolveTheme());
        });
    </script>

    <style>
        #mobile-nav-overlay {
            opacity: 0;
            transform: translateY(-12px);
            transition: opacity 0.25s ease, transform 0.25s ease;
        }
        #mobile-nav-overlay.mp-menu-open {
            opacity: 1;
            transform: translateY(0);
        }
    </style>

    @yield('scripts')

</body>
</html>

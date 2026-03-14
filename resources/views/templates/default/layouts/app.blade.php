<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
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

    <script src="https://cdn.tailwindcss.com"></script>

    @yield('head')
</head>
<body class="flex flex-col min-h-full bg-white text-gray-900 antialiased">

    @include('template::partials.header')

    <main class="flex-1">
        @yield('content')
    </main>

    @include('template::partials.footer')

    @yield('scripts')

</body>
</html>

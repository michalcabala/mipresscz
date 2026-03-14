<header class="bg-white border-b border-gray-200 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ url('/') }}" class="text-xl font-bold text-gray-900 hover:text-gray-700 transition-colors">
                {{ config('app.name', 'miPress') }}
            </a>
            @include('template::partials.nav')
        </div>
    </div>
</header>

<div class="mason-brick mason-latest-entries bg-white dark:bg-gray-950 py-24 sm:py-32 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-end justify-between mb-12">
            <div>
                @if($eyebrow ?? null)
                <span class="text-blue-600 dark:text-blue-400 text-sm font-semibold font-mono uppercase tracking-widest">{{ $eyebrow }}</span>
                @endif
                @if($heading ?? null)
                <h2 class="mt-3 text-4xl sm:text-5xl font-bold text-gray-900 dark:text-white leading-tight">{{ $heading }}</h2>
                @endif
            </div>
            @if(($view_all_label ?? null) && ($view_all_url ?? null))
            <a href="{{ $view_all_url }}" class="hidden sm:inline-flex items-center gap-1 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                {{ $view_all_label }}
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
            </a>
            @endif
        </div>

        @php $count = $entries->count(); $gridClass = match(true) { $count <= 2 => 'grid-cols-1 md:grid-cols-2', default => 'grid-cols-1 md:grid-cols-3', }; @endphp
        <div class="grid {{ $gridClass }} gap-8">
            @foreach($entries as $article)
            <article class="group flex flex-col bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden hover:border-blue-300 dark:hover:border-blue-700 transition-all duration-200">
                @if($article->featured_image_id ?? null)
                <div class="aspect-video overflow-hidden bg-gray-200 dark:bg-gray-800">
                    <x-curator-glider
                        :media="$article->featured_image_id"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        width="600"
                        height="338"
                    />
                </div>
                @else
                <div class="aspect-video bg-gradient-to-br from-blue-100 dark:from-blue-950 to-blue-50 dark:to-gray-900 flex items-center justify-center">
                    <span class="text-4xl opacity-30">📄</span>
                </div>
                @endif

                <div class="flex flex-col flex-1 p-6">
                    @if($article->published_at)
                    <time class="text-xs text-gray-500 dark:text-gray-500 mb-3 font-mono">
                        {{ $article->published_at->translatedFormat('j. F Y') }}
                    </time>
                    @endif
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 leading-snug group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2">
                        <a href="{{ url($article->uri) }}">{{ $article->title }}</a>
                    </h3>
                    @if($article->meta_description ?? null)
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed flex-1 line-clamp-3">{{ $article->meta_description }}</p>
                    @endif
                    <a href="{{ url($article->uri) }}"
                       class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                        {{ __('Číst dál') }}
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </a>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</div>

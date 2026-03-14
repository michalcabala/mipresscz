<a href="{{ url('/') }}"
   class="px-3 py-2 rounded-lg text-sm font-medium transition-colors
          {{ request()->is('/') ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/50' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800' }}">
    {{ __('Domů') }}
</a>
@foreach($navEntries ?? [] as $item)
    <a href="{{ url($item->uri) }}"
       class="px-3 py-2 rounded-lg text-sm font-medium transition-colors
              {{ request()->is(ltrim($item->uri, '/')) ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/50' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800' }}">
        {{ $item->title }}
    </a>
@endforeach

<div class="flex flex-col items-center justify-center py-32 px-4 text-center">
    <p class="text-8xl font-bold text-gray-200 dark:text-gray-700 select-none">503</p>
    <h1 class="mt-4 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('errors.503_heading') }}</h1>
    <p class="mt-2 text-gray-500 dark:text-gray-400 max-w-md">
        {{ __('errors.503_message') }}
    </p>
    <a href="{{ url('/') }}"
       class="mt-8 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
        &larr; {{ __('errors.back_home') }}
    </a>
</div>

<nav class="hidden sm:flex items-center gap-6">
    <a href="{{ url('/') }}"
       class="text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors {{ request()->is('/') ? 'text-gray-900' : '' }}">
        Domů
    </a>
</nav>

<button type="button"
        class="sm:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors"
        aria-label="Otevřít menu">
    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
</button>

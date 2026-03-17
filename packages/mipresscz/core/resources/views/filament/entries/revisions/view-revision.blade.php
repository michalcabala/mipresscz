<div class="space-y-6">
    <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-2xl border border-gray-200 p-4 dark:border-white/10">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('revisions.revision_number', ['number' => $revision->revision_number]) }}</p>
            <p class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ $revision->type->label() }}</p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $revision->created_at?->diffForHumans() }}</p>
        </div>

        <div class="rounded-2xl border border-gray-200 p-4 dark:border-white/10">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('revisions.author') }}</p>
            <p class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ $revision->user?->name ?? 'System' }}</p>
            @if (filled($revision->note))
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $revision->note }}</p>
            @endif
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-white/10 dark:bg-gray-950/40">
        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('revisions.snapshot') }}</p>
        <pre class="mt-3 overflow-x-auto text-xs text-gray-700 dark:text-gray-200">{{ json_encode($revision->content ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
    </div>
</div>

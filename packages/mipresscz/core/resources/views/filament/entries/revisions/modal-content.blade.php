<div class="space-y-6">
    <dl class="grid gap-4 sm:grid-cols-2">
        <div>
            <dt class="text-sm font-medium text-gray-500">{{ __('content.entry_fields.title') }}</dt>
            <dd class="mt-1 text-sm text-gray-950 dark:text-white">{{ $revision->title }}</dd>
        </div>

        <div>
            <dt class="text-sm font-medium text-gray-500">{{ __('content.revision_fields.action') }}</dt>
            <dd class="mt-1 text-sm text-gray-950 dark:text-white">{{ __('content.revision_fields.action_'.$revision->action) }}</dd>
        </div>

        <div>
            <dt class="text-sm font-medium text-gray-500">{{ __('content.entry_fields.status') }}</dt>
            <dd class="mt-1 text-sm text-gray-950 dark:text-white">{{ \MiPressCz\Core\Enums\EntryStatus::from($revision->status)->getLabel() }}</dd>
        </div>

        <div>
            <dt class="text-sm font-medium text-gray-500">{{ __('content.revision_fields.created_at') }}</dt>
            <dd class="mt-1 text-sm text-gray-950 dark:text-white">{{ $revision->created_at?->isoFormat('LLL') }}</dd>
        </div>

        <div>
            <dt class="text-sm font-medium text-gray-500">{{ __('content.revision_fields.user') }}</dt>
            <dd class="mt-1 text-sm text-gray-950 dark:text-white">{{ $revision->user?->name ?? '-' }}</dd>
        </div>

        @if (filled($revision->message))
            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('content.revision_fields.note') }}</dt>
                <dd class="mt-1 text-sm text-gray-950 dark:text-white">{{ $revision->message }}</dd>
            </div>
        @endif
    </dl>

    <div class="space-y-2">
        <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ __('content.revision_fields.data') }}</h4>
        <pre class="overflow-x-auto rounded-lg bg-gray-950 p-4 text-xs text-white">{{ json_encode($revision->data ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
    </div>

    <div class="space-y-2">
        <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ __('content.revision_fields.content') }}</h4>
        <pre class="overflow-x-auto rounded-lg bg-gray-950 p-4 text-xs text-white">{{ json_encode($revision->content ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
    </div>
</div>

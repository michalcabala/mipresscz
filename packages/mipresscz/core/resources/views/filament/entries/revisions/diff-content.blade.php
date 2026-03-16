<div class="space-y-6 py-2">
    {{-- Header --}}
    <div class="flex flex-wrap items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
        @if($previousRevision)
            <span>{{ $previousRevision->created_at?->isoFormat('LLL') }}</span>
            <span class="text-gray-400">→</span>
            <span class="font-medium text-gray-900 dark:text-white">{{ $revision->created_at?->isoFormat('LLL') }}</span>
        @else
            <span class="font-medium text-gray-900 dark:text-white">{{ $revision->created_at?->isoFormat('LLL') }}</span>
        @endif
    </div>

    @if(! $previousRevision)
        <div class="rounded-lg bg-gray-50 dark:bg-gray-800 px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
            {{ __('content.revision_fields.no_previous_revision') }}
        </div>
    @elseif(empty($diff))
        <div class="rounded-lg bg-gray-50 dark:bg-gray-800 px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
            {{ __('content.revision_fields.no_changes') }}
        </div>
    @else
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($diff as $key => $change)
                <div class="py-4">
                    <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">
                        {{ $change['label'] }}
                    </div>

                    @if(isset($change['diff_html']))
                        {{-- Word-level diff --}}
                        <div class="leading-relaxed text-sm text-gray-800 dark:text-gray-200">
                            {!! $change['diff_html'] !!}
                        </div>
                    @elseif(! is_null($change['old']) || ! is_null($change['new']))
                        {{-- Side-by-side comparison --}}
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-lg border border-red-100 bg-red-50 p-3 dark:border-red-900/50 dark:bg-red-950/30">
                                <div class="mb-1 text-xs font-medium text-red-500 dark:text-red-400">
                                    {{ __('content.revision_fields.before') }}
                                </div>
                                <div class="whitespace-pre-wrap break-words text-sm text-gray-700 dark:text-gray-300">
                                    @if(is_scalar($change['old']) || is_null($change['old']))
                                        {{ $change['old'] ?? '–' }}
                                    @else
                                        <code class="text-xs">{{ json_encode($change['old'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</code>
                                    @endif
                                </div>
                            </div>
                            <div class="rounded-lg border border-green-100 bg-green-50 p-3 dark:border-green-900/50 dark:bg-green-950/30">
                                <div class="mb-1 text-xs font-medium text-green-600 dark:text-green-400">
                                    {{ __('content.revision_fields.after') }}
                                </div>
                                <div class="whitespace-pre-wrap break-words text-sm text-gray-700 dark:text-gray-300">
                                    @if(is_scalar($change['new']) || is_null($change['new']))
                                        {{ $change['new'] ?? '–' }}
                                    @else
                                        <code class="text-xs">{{ json_encode($change['new'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</code>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

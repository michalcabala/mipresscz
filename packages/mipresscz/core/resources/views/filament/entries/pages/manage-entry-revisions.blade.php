<x-filament-panels::page>
    @php
        $revisions = $this->getRevisions();
        $revisionOptions = $this->getRevisionOptions();
        $diff = $this->getComparisonDiff();
    @endphp

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)]">
        <x-filament::section>
            <x-slot name="heading">
                {{ __('revisions.timeline_heading') }}
            </x-slot>

            <x-slot name="description">
                {{ __('revisions.timeline_description') }}
            </x-slot>

            @if ($revisions->isEmpty())
                <div class="rounded-xl border border-dashed border-gray-300 p-6 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                    {{ __('revisions.empty') }}
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($revisions as $revision)
                        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div class="space-y-3">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 dark:bg-white/10 dark:text-gray-200">
                                            <x-filament::icon :icon="$revision->type->icon()" class="h-4 w-4" />
                                            {{ __('revisions.revision_number', ['number' => $revision->revision_number]) }}
                                        </span>

                                        <span @class([
                                            'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold',
                                            'bg-info-50 text-info-700 dark:bg-info-500/10 dark:text-info-400' => $revision->type->color() === 'info',
                                            'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400' => $revision->type->color() === 'success',
                                            'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-300' => $revision->type->color() === 'gray',
                                            'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400' => $revision->type->color() === 'warning',
                                        ])>
                                            {{ $revision->type->label() }}
                                        </span>

                                        @if ($revision->type->value === 'published')
                                            <span class="inline-flex items-center rounded-full bg-success-50 px-3 py-1 text-xs font-semibold text-success-700 dark:bg-success-500/10 dark:text-success-400">
                                                {{ __('revisions.published_badge') }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="space-y-1">
                                        <p class="text-sm font-medium text-gray-950 dark:text-white">
                                            {{ $revision->user?->name ?? 'System' }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $revision->created_at?->diffForHumans() }}
                                        </p>
                                        @if (filled($revision->note))
                                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                                {{ $revision->note }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                        {{ $this->getTimelineAction('viewRevision', $revision) }}

                                    <x-filament::button
                                        color="info"
                                        outlined
                                        wire:click="compareWithCurrent('{{ $revision->getKey() }}')"
                                    >
                                        {{ __('revisions.actions.compare') }}
                                    </x-filament::button>

                                    @if ($restoreAction = $this->getTimelineAction('restoreRevision', $revision))
                                        {{ $restoreAction }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $revisions->links() }}
                </div>
            @endif
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                {{ __('revisions.compare_heading') }}
            </x-slot>

            <x-slot name="description">
                {{ __('revisions.compare_description') }}
            </x-slot>

            <div class="grid grid-cols-1 gap-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-[1fr_auto_1fr] md:items-end">
                    <label class="grid gap-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('revisions.old_version') }}</span>
                        <select wire:model.live="leftRevision" class="rounded-xl border-gray-300 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            @foreach ($revisionOptions as $revisionOption)
                                <option value="{{ $revisionOption->getKey() }}">
                                    {{ __('revisions.revision_number', ['number' => $revisionOption->revision_number]) }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <x-filament::button color="gray" outlined wire:click="swapComparedRevisions">
                        {{ __('revisions.actions.swap') }}
                    </x-filament::button>

                    <label class="grid gap-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('revisions.new_version') }}</span>
                        <select wire:model.live="rightRevision" class="rounded-xl border-gray-300 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            <option value="current">{{ __('revisions.current_version') }}</option>
                            @foreach ($revisionOptions as $revisionOption)
                                <option value="{{ $revisionOption->getKey() }}">
                                    {{ __('revisions.revision_number', ['number' => $revisionOption->revision_number]) }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                </div>

                <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-white/10 dark:bg-gray-950/40">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $this->getComparisonLabel($this->leftRevision) }}</p>
                        <pre class="mt-3 overflow-x-auto text-xs text-gray-600 dark:text-gray-300">{{ json_encode($this->getComparisonSnapshot($this->leftRevision), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-white/10 dark:bg-gray-950/40">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $this->getComparisonLabel($this->rightRevision) }}</p>
                        <pre class="mt-3 overflow-x-auto text-xs text-gray-600 dark:text-gray-300">{{ json_encode($this->getComparisonSnapshot($this->rightRevision), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                </div>

                @foreach (['changed', 'added', 'removed'] as $section)
                    <div class="rounded-2xl border border-gray-200 p-4 dark:border-white/10">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ __('revisions.'.$section) }}
                        </h3>

                        @if (blank($diff[$section]))
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('revisions.no_changes') }}
                            </p>
                        @else
                            <div class="mt-3 space-y-3">
                                @foreach ($diff[$section] as $item)
                                    <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-950/40">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                            {{ $item['field'] }}
                                        </p>
                                        <div class="mt-2 grid grid-cols-1 gap-3 lg:grid-cols-2">
                                            <div>
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('revisions.old_version') }}</p>
                                                <pre class="mt-1 overflow-x-auto text-xs text-gray-700 dark:text-gray-200">{{ json_encode($item['old'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                                            </div>
                                            <div>
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('revisions.new_version') }}</p>
                                                <pre class="mt-1 overflow-x-auto text-xs text-gray-700 dark:text-gray-200">{{ json_encode($item['new'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>

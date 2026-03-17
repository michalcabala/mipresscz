<x-filament-panels::page>
    <div class="space-y-4" wire:poll.visible.{{ $this->getAutosaveIntervalSeconds() }}s="autosave">
        <div class="flex justify-end">
            <div class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-600 dark:bg-white/10 dark:text-gray-300">
                <span wire:loading wire:target="autosave">
                    {{ __('revisions.autosave.saving') }}
                </span>

                <span wire:loading.remove wire:target="autosave">
                    {{ $this->getAutosaveStatusLabel() }}
                </span>
            </div>
        </div>

        {{ $this->content }}
    </div>
</x-filament-panels::page>

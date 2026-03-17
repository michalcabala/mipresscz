<?php

declare(strict_types=1);

namespace MiPressCz\Core\Filament\Resources\Entries\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\WithPagination;
use MiPressCz\Core\Filament\Resources\Entries\EntryResource;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\Revision;
use MiPressCz\Core\Services\RevisionService;

class ManageEntryRevisions extends Page
{
    use InteractsWithRecord;
    use WithPagination;

    protected static string $resource = EntryResource::class;

    protected string $view = 'mipresscz-core::filament.entries.pages.manage-entry-revisions';

    public string $leftRevision = '';

    public string $rightRevision = 'current';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        abort_unless($this->canViewRevisions(), 403);

        /** @var Entry $entry */
        $entry = $this->getRecord();
        $revisionIds = $entry->revisions()->pluck('id')->all();

        $this->leftRevision = (string) ($revisionIds[1] ?? $revisionIds[0] ?? '');
    }

    public static function getNavigationIcon(): Heroicon|string|null
    {
        return Heroicon::OutlinedClock;
    }

    public static function getNavigationLabel(): string
    {
        return __('revisions.plural_label');
    }

    public static function canAccess(array $parameters = []): bool
    {
        $record = $parameters['record'] ?? null;

        if (! $record instanceof Entry) {
            $record = Entry::query()->find($record);
        }

        return $record instanceof Entry
            && auth()->user()?->can('viewRevisions', $record);
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('revisions.plural_label');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return (string) $this->getRecordTitle();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('backToEntry')
                ->label(__('content.actions.edit_entry'))
                ->icon(Heroicon::OutlinedPencilSquare)
                ->color('gray')
                ->url($this->getResourceUrl('edit')),
        ];
    }

    public function getRevisions(): LengthAwarePaginator
    {
        /** @var Entry $entry */
        $entry = $this->getRecord();

        return $entry->revisions()
            ->with('user')
            ->paginate(perPage: 10);
    }

    /**
     * @return Collection<int, Revision>
     */
    public function getRevisionOptions(): Collection
    {
        /** @var Entry $entry */
        $entry = $this->getRecord();

        return $entry->revisions()->get();
    }

    /**
     * @return array{
     *     added: array<int, array{field: string, old: mixed, new: mixed}>,
     *     removed: array<int, array{field: string, old: mixed, new: mixed}>,
     *     changed: array<int, array{field: string, old: mixed, new: mixed}>
     * }
     */
    public function getComparisonDiff(): array
    {
        return app(RevisionService::class)->diffSnapshots(
            $this->getComparisonSnapshot($this->leftRevision),
            $this->getComparisonSnapshot($this->rightRevision),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function getComparisonSnapshot(string $selection): array
    {
        if ($selection === 'current') {
            /** @var Entry $entry */
            $entry = $this->getRecord();

            return $entry->getRevisionSnapshot();
        }

        return $this->findRevision($selection)?->content ?? [];
    }

    public function getComparisonLabel(string $selection): string
    {
        if ($selection === 'current') {
            return __('revisions.current_version');
        }

        $revision = $this->findRevision($selection);

        if (! $revision instanceof Revision) {
            return __('revisions.current_version');
        }

        return __('revisions.revision_number', ['number' => $revision->revision_number]);
    }

    public function compareWithCurrent(string $revision): void
    {
        abort_unless($this->canCompareRevisions(), 403);

        $this->leftRevision = $revision;
        $this->rightRevision = 'current';
    }

    public function swapComparedRevisions(): void
    {
        abort_unless($this->canCompareRevisions(), 403);

        [$this->leftRevision, $this->rightRevision] = [$this->rightRevision, $this->leftRevision];
    }

    public function getTimelineAction(string $name, Revision $revision): ?Action
    {
        return $this->getAction($name, false)?->getClone()->arguments([
            'revision' => $revision->getKey(),
        ]);
    }

    public function viewRevisionAction(): Action
    {
        return Action::make('viewRevision')
            ->label(__('revisions.actions.view'))
            ->icon('heroicon-o-eye')
            ->color('gray')
            ->modalSubmitAction(false)
            ->modalWidth('4xl')
            ->modalHeading(fn (array $arguments): string => $this->getComparisonLabel((string) ($arguments['revision'] ?? '')))
            ->modalContent(function (array $arguments): View {
                $revision = $this->findRevision((string) ($arguments['revision'] ?? ''));

                abort_unless($revision instanceof Revision, 404);

                return view('mipresscz-core::filament.entries.revisions.view-revision', [
                    'revision' => $revision,
                ]);
            });
    }

    public function restoreRevisionAction(): Action
    {
        return Action::make('restoreRevision')
            ->label(__('revisions.actions.restore'))
            ->icon('heroicon-o-arrow-path')
            ->color('warning')
            ->requiresConfirmation()
            ->visible(function (array $arguments): bool {
                $revision = $this->findRevision((string) ($arguments['revision'] ?? ''));

                return $revision instanceof Revision
                    && $this->canRestoreRevision();
            })
            ->modalDescription(fn (array $arguments): string => $this->buildRestorePreview((string) ($arguments['revision'] ?? '')))
            ->action(function (array $arguments): void {
                abort_unless($this->canRestoreRevision(), 403);

                $revision = $this->findRevision((string) ($arguments['revision'] ?? ''));

                abort_unless($revision instanceof Revision, 404);

                /** @var Entry $entry */
                $entry = app(RevisionService::class)->restoreRevision($revision);
                $this->record = $entry;
                $this->rightRevision = 'current';

                Notification::make()
                    ->title(__('revisions.messages.restored', ['number' => $revision->revision_number]))
                    ->success()
                    ->send();

                $this->redirect($this->getResourceUrl('edit'));
            });
    }

    public function canViewRevisions(): bool
    {
        return auth()->user()?->can('viewRevisions', $this->getRecord()) === true;
    }

    public function canCompareRevisions(): bool
    {
        return auth()->user()?->can('compareRevisions', $this->getRecord()) === true;
    }

    public function canRestoreRevision(): bool
    {
        return auth()->user()?->can('restoreRevision', $this->getRecord()) === true;
    }

    private function buildRestorePreview(string $revisionId): string
    {
        $revision = $this->findRevision($revisionId);

        if (! $revision instanceof Revision) {
            return __('revisions.messages.restore_confirm');
        }

        $diff = app(RevisionService::class)->diffSnapshots(
            $revision->content ?? [],
            $this->getComparisonSnapshot('current'),
        );

        $fields = collect($diff['changed'])
            ->pluck('field')
            ->merge(collect($diff['added'])->pluck('field'))
            ->merge(collect($diff['removed'])->pluck('field'))
            ->filter()
            ->take(5)
            ->implode(', ');

        if ($fields === '') {
            return __('revisions.messages.restore_confirm');
        }

        return __('revisions.messages.restore_preview', ['fields' => $fields]);
    }

    private function findRevision(string $revisionId): ?Revision
    {
        if ($revisionId === '' || $revisionId === 'current') {
            return null;
        }

        /** @var Entry $entry */
        $entry = $this->getRecord();

        return $entry->revisions()
            ->with('user')
            ->find($revisionId);
    }
}

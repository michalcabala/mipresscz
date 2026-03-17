<?php

declare(strict_types=1);

namespace MiPressCz\Core\Filament\Resources\Entries\Pages;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Filament\Resources\Entries\EntryResource;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\Locale;
use MiPressCz\Core\Services\RevisionService;

class EditEntry extends EditRecord
{
    protected static string $resource = EntryResource::class;

    protected string $view = 'mipresscz-core::filament.entries.pages.edit-entry';

    public string $autosaveStatus = 'saved';

    public ?string $autosaveSavedAt = null;

    public string $lastAutosaveHash = '';

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->syncAutosaveStateFromLatestRevision();
    }

    protected function getHeaderActions(): array
    {
        return [
            ...$this->getWorkflowActions(),
            ...$this->getLocaleActions(),
            DeleteAction::make()
                ->color('danger')
                ->icon(Heroicon::OutlinedTrash)
                ->before(function (Entry $record, DeleteAction $action): void {
                    if ($record->is_homepage) {
                        Notification::make()
                            ->title(__('content.messages.cannot_delete_homepage'))
                            ->danger()
                            ->send();
                        $action->cancel();
                    }
                }),
        ];
    }

    protected function resolveRecord(int|string $key): Entry
    {
        return Entry::with(['translations', 'origin.translations', 'blueprint', 'collection', 'revisions'])
            ->findOrFail($key);
    }

    protected function getFormActions(): array
    {
        return [];
    }

    public function autosave(): void
    {
        /** @var Entry $record */
        $record = $this->getRecord();

        if (! auth()->user()?->can('update', $record)) {
            return;
        }

        $this->autosaveStatus = 'saving';

        $snapshot = $record->buildRevisionSnapshot(
            $this->mutateFormDataBeforeSave($this->form->getStateSnapshot()),
        );

        $snapshotHash = app(RevisionService::class)->snapshotHash($snapshot);

        if ($snapshotHash === $this->lastAutosaveHash) {
            $this->autosaveStatus = 'saved';

            return;
        }

        app(RevisionService::class)->createAutosaveRevision($record, $snapshot);

        $this->lastAutosaveHash = $snapshotHash;
        $this->autosaveStatus = 'saved';
        $this->autosaveSavedAt = now()->toIso8601String();
    }

    public function getAutosaveIntervalSeconds(): int
    {
        return max(1, (int) config('mipress-revisions.autosave_interval', 60));
    }

    public function getAutosaveStatusLabel(): string
    {
        if ($this->autosaveStatus === 'saving') {
            return __('revisions.autosave.saving');
        }

        if (filled($this->autosaveSavedAt)) {
            return __('revisions.autosave.saved_at', [
                'time' => Carbon::parse($this->autosaveSavedAt)->format('H:i:s'),
            ]);
        }

        return __('revisions.autosave.saved');
    }

    /** @return list<Action|ActionGroup> */
    protected function getWorkflowActions(): array
    {
        if ($this->isPublishedEntry()) {
            return [
                $this->getSavePublishedAction(),
                $this->getWorkflowMoreActions(),
            ];
        }

        return [
            $this->getPublishDraftAction(),
            $this->getSaveDraftAction(),
            $this->getWorkflowMoreActions(),
        ];
    }

    protected function isPublishedEntry(): bool
    {
        /** @var Entry $record */
        $record = $this->getRecord();

        return $record->status === EntryStatus::Published;
    }

    protected function getSaveDraftAction(): Action
    {
        return Action::make('save')
            ->label(__('content.actions.save_draft'))
            ->color('gray')
            ->action(fn () => $this->save())
            ->keyBindings(['mod+s']);
    }

    protected function getSavePublishedAction(): Action
    {
        return Action::make('save')
            ->label(__('content.actions.save'))
            ->color('gray')
            ->action(fn () => $this->save())
            ->keyBindings(['mod+s']);
    }

    protected function getPreviewAction(): Action
    {
        /** @var Entry $record */
        $record = $this->getRecord();

        return Action::make('preview')
            ->label(__('content.actions.preview'))
            ->icon(Heroicon::OutlinedEye)
            ->color('gray')
            ->url(fn (): string => $record->getPreviewUrl())
            ->openUrlInNewTab();
    }

    protected function getPublishDraftAction(): Action
    {
        /** @var Entry $record */
        $record = $this->getRecord();

        return Action::make('publish')
            ->label(__('content.actions.publish'))
            ->icon(Heroicon::OutlinedCheckCircle)
            ->color('primary')
            ->visible(fn (): bool => auth()->user()->can('publish', $record))
            ->action(function (): void {
                $this->data['status'] = EntryStatus::Published->value;
                $this->data['published_at'] ??= now()->toDateTimeString();
                $this->save();
            });
    }

    protected function getUnpublishAction(): Action
    {
        /** @var Entry $record */
        $record = $this->getRecord();

        return Action::make('unpublish')
            ->label(__('content.actions.unpublish'))
            ->color('warning')
            ->icon(Heroicon::OutlinedArrowUturnLeft)
            ->visible(fn (): bool => auth()->user()->can('publish', $record))
            ->requiresConfirmation()
            ->action(function (): void {
                $this->data['status'] = EntryStatus::Draft->value;
                $this->data['published_at'] = null;
                $this->save();
            });
    }

    protected function getWorkflowMoreActions(): ActionGroup
    {
        $actions = [
            $this->getPreviewAction(),
        ];

        if ($this->isPublishedEntry()) {
            $actions[] = $this->getUnpublishAction();
        }

        return ActionGroup::make($actions)
            ->label(__('content.actions.more_actions'))
            ->icon(Heroicon::OutlinedEllipsisHorizontal)
            ->color('gray')
            ->button()
            ->dropdownPlacement('bottom-end');
    }

    /**
     * Inline round flag <img> tag that works inside Filament's truncated
     * dropdown-item labels (no wrapper spans, just a plain <img>).
     */
    protected function flagImg(?string $flagFile, string $alt, string $sizeClass = 'size-5'): string
    {
        if (! $flagFile) {
            return '';
        }

        $url = e(asset("assets/flags/{$flagFile}"));

        return '<img src="'.$url.'" alt="'.e($alt).'" class="inline '.$sizeClass.' rounded-full object-cover align-middle" />';
    }

    /** @return list<Action|ActionGroup> */
    protected function getLocaleActions(): array
    {
        /** @var Entry $record */
        $record = $this->getRecord();
        $translations = $record->getTranslations();
        $missingLocales = $record->getMissingLocales();

        /** @var Collection<string, Locale> $localeMap */
        $localeMap = locales()->getActive()->keyBy('code');

        if ($localeMap->count() <= 1) {
            return [];
        }

        $currentLocale = $record->locale;
        $currentModel = $localeMap->get($currentLocale);

        $existingCount = $translations->count();
        $totalCount = $existingCount + count($missingLocales);

        $switchItems = $translations
            ->reject(fn (Entry $entry) => $entry->id === $record->id)
            ->map(function (Entry $entry, string $locale) use ($localeMap): Action {
                $model = $localeMap->get($locale);
                $flag = $this->flagImg($model?->flag, $locale, 'size-4');
                $name = $model?->native_name ?? strtoupper($locale);

                return Action::make("locale_switch_{$locale}")
                    ->label(new HtmlString($flag ? "{$flag} ".e($name) : e($name)))
                    ->badge(strtoupper($locale))
                    ->badgeColor('gray')
                    ->color('gray')
                    ->url(static::getResource()::getUrl('edit', ['record' => $entry->id]));
            })
            ->values()
            ->all();

        $createItems = collect($missingLocales)
            ->map(function (string $locale) use ($record, $localeMap): Action {
                $model = $localeMap->get($locale);
                $flag = $this->flagImg($model?->flag, $locale, 'size-4');
                $name = $model?->native_name ?? strtoupper($locale);

                return Action::make("locale_create_{$locale}")
                    ->label(new HtmlString($flag ? "{$flag} ".e($name) : e($name)))
                    ->badge(__('content.actions.add_short'))
                    ->badgeColor('success')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading(__('content.actions.create_translation_for', ['locale' => $name]))
                    ->modalDescription(__('content.actions.create_translation_confirm', ['locale' => $name]))
                    ->action(function () use ($record, $locale): void {
                        $origin = $record->getOrigin();

                        $translation = Entry::create([
                            'collection_id' => $origin->collection_id,
                            'blueprint_id' => $origin->blueprint_id,
                            'origin_id' => $origin->id,
                            'locale' => $locale,
                            'title' => $origin->title,
                            'slug' => $origin->slug.'-'.$locale,
                            'status' => $origin->status,
                            'author_id' => auth()->id() ?? $origin->author_id,
                            'parent_id' => $origin->parent_id,
                            'order' => $origin->order,
                            'data' => $origin->getNonTranslatableData(),
                            'content' => null,
                            'featured_image_id' => $origin->featured_image_id,
                        ]);

                        $this->redirect(static::getResource()::getUrl('edit', ['record' => $translation->id]));
                    });
            })
            ->all();

        if (empty($switchItems) && empty($createItems)) {
            return [];
        }

        $dropdownItems = [];

        if (! empty($switchItems)) {
            $dropdownItems[] = ActionGroup::make($switchItems)->dropdown(false);
        }

        if (! empty($createItems)) {
            $dropdownItems[] = ActionGroup::make($createItems)->dropdown(false);
        }

        $triggerFlag = $this->flagImg($currentModel?->flag, $currentLocale, 'size-5');
        $triggerText = strtoupper($currentLocale);
        $triggerHtml = $triggerFlag
            ? "{$triggerFlag} {$triggerText}"
            : $triggerText;

        return [
            ActionGroup::make($dropdownItems)
                ->label(new HtmlString($triggerHtml))
                ->badge($totalCount > 1 ? "{$existingCount}/{$totalCount}" : null)
                ->badgeColor($existingCount < $totalCount ? 'warning' : 'success')
                ->color('gray')
                ->button()
                ->dropdownPlacement('bottom-end'),
        ];
    }

    protected function afterSave(): void
    {
        $this->syncAutosaveStateFromLatestRevision();
    }

    private function syncAutosaveStateFromLatestRevision(): void
    {
        /** @var Entry $record */
        $record = $this->getRecord()->fresh(['revisions']) ?? $this->getRecord();
        $latestRevision = $record->latestRevision;

        $this->lastAutosaveHash = app(RevisionService::class)->snapshotHash(
            $latestRevision?->content ?? $record->getRevisionSnapshot(),
        );
        $this->autosaveSavedAt = $latestRevision?->created_at?->toIso8601String();
        $this->autosaveStatus = 'saved';
        $this->record = $record;
    }
}

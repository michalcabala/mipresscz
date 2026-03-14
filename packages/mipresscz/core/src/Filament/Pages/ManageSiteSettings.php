<?php

namespace MiPressCz\Core\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Services\CacheService;

class ManageSiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'mipresscz-core::filament.pages.manage-site-settings';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('manage.settings') ?? false;
    }

    /** @var array<string, mixed> */
    public array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return __('settings.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('settings.navigation_label');
    }

    public function getTitle(): string
    {
        return __('settings.page_title');
    }

    public static function getNavigationSort(): ?int
    {
        return 20;
    }

    public static function getNavigationIcon(): \BackedEnum|string|null
    {
        return 'heroicon-o-cog-6-tooth';
    }

    public function mount(): void
    {
        $this->form->fill([
            'homepage_entry_id' => Entry::query()->where('is_homepage', true)->value('id'),
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make(__('settings.homepage.section_title'))
                    ->description(__('settings.homepage.section_description'))
                    ->schema([
                        Select::make('homepage_entry_id')
                            ->label(__('settings.homepage.entry'))
                            ->helperText(__('settings.homepage.entry_hint'))
                            ->placeholder(__('settings.homepage.none'))
                            ->options(fn (): array => $this->getPagesOptions())
                            ->searchable()
                            ->nullable(),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('settings.save'))
                ->action('save'),
            Action::make('clear_cache')
                ->label(__('settings.cache.clear'))
                ->color('gray')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->modalHeading(__('settings.cache.confirm_heading'))
                ->modalDescription(__('settings.cache.confirm_description'))
                ->action(function (): void {
                    app(CacheService::class)->flushAll();

                    Notification::make()
                        ->title(__('settings.cache.cleared'))
                        ->success()
                        ->send();
                }),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $entryId = $data['homepage_entry_id'] ?? null;

        Entry::query()->where('is_homepage', true)->update(['is_homepage' => false]);

        if ($entryId) {
            Entry::query()->where('id', $entryId)->update(['is_homepage' => true]);
        }

        Notification::make()
            ->title(__('settings.saved'))
            ->success()
            ->send();
    }

    /**
     * Returns entries from the 'pages' collection as Select options.
     *
     * @return array<string, string>
     */
    public function getPagesOptions(): array
    {
        $pagesCollection = Collection::query()
            ->where('handle', 'pages')
            ->first();

        if (! $pagesCollection) {
            return [];
        }

        return Entry::query()
            ->where('collection_id', $pagesCollection->id)
            ->whereNull('origin_id')
            ->orderBy('title')
            ->pluck('title', 'id')
            ->all();
    }
}

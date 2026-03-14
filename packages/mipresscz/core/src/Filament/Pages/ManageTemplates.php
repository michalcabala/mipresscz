<?php

namespace MiPressCz\Core\Filament\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use MiPressCz\Core\Services\TemplateManager;

class ManageTemplates extends Page
{
    protected string $view = 'mipresscz-core::filament.pages.manage-templates';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('manage.settings') ?? false;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('templates.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('templates.navigation_label');
    }

    public function getTitle(): string
    {
        return __('templates.page_title');
    }

    public static function getNavigationSort(): ?int
    {
        return 30;
    }

    public static function getNavigationIcon(): \BackedEnum|string|null
    {
        return 'heroicon-o-paint-brush';
    }

    /** @return Collection<int, array<string, mixed>> */
    public function getTemplates(): Collection
    {
        return app(TemplateManager::class)->getAvailable();
    }

    public function getActiveTemplate(): string
    {
        return app(TemplateManager::class)->getActive();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function activate(string $slug): void
    {
        abort_unless(
            auth()->user()?->can('manage.global_sets'),
            403,
            __('templates.unauthorized')
        );

        try {
            app(TemplateManager::class)->setActive($slug);
        } catch (\InvalidArgumentException $e) {
            Notification::make()
                ->title($e->getMessage())
                ->danger()
                ->send();

            return;
        }

        Notification::make()
            ->title(__('templates.activated'))
            ->success()
            ->send();
    }
}

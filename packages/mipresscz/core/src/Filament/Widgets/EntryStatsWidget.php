<?php

namespace MiPressCz\Core\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Models\Entry;

class EntryStatsWidget extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $total = Entry::query()->count();
        $published = Entry::query()->where('status', EntryStatus::Published)->count();
        $draft = Entry::query()->where('status', EntryStatus::Draft)->count();
        $scheduled = Entry::query()->where('status', EntryStatus::Scheduled)->count();

        return [
            Stat::make(__('content.stats.total_entries'), $total)
                ->icon('heroicon-o-document-text')
                ->color('gray'),

            Stat::make(__('content.stats.published'), $published)
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make(__('content.stats.drafts'), $draft)
                ->icon('heroicon-o-pencil')
                ->color('warning'),

            Stat::make(__('content.stats.scheduled'), $scheduled)
                ->icon('heroicon-o-clock')
                ->color('info'),
        ];
    }
}

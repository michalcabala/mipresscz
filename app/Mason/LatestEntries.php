<?php

namespace App\Mason;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use Throwable;

class LatestEntries extends Brick
{
    public static function getId(): string
    {
        return 'latest-entries';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedNewspaper;
    }

    public static function getLabel(): string
    {
        return __('bricks.latest_entries');
    }

    /**
     * @throws Throwable
     */
    public static function toHtml(array $config, ?array $data = null): ?string
    {
        $collectionHandle = $config['collection'] ?? 'articles';
        $limit = (int) ($config['limit'] ?? 3);

        $entries = Entry::query()
            ->with(['featuredImage'])
            ->whereHas('collection', fn ($q) => $q->where('handle', $collectionHandle))
            ->published()
            ->where('locale', app()->getLocale())
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();

        if ($entries->isEmpty()) {
            return '';
        }

        return view('mason.latest-entries', [
            'eyebrow' => $config['eyebrow'] ?? null,
            'heading' => $config['heading'] ?? null,
            'view_all_label' => $config['view_all_label'] ?? null,
            'view_all_url' => $config['view_all_url'] ?? null,
            'entries' => $entries,
        ])->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                TextInput::make('eyebrow')
                    ->label(__('bricks.fields.eyebrow'))
                    ->columnSpanFull(),
                TextInput::make('heading')
                    ->label(__('bricks.fields.heading'))
                    ->required()
                    ->columnSpanFull(),
                Select::make('collection')
                    ->label(__('bricks.fields.collection'))
                    ->options(fn () => Collection::query()->pluck('name', 'handle')->toArray())
                    ->default('articles')
                    ->required(),
                Select::make('limit')
                    ->label(__('bricks.fields.limit'))
                    ->options([
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                        '6' => '6',
                    ])
                    ->default('3'),
                TextInput::make('view_all_label')
                    ->label(__('bricks.fields.view_all_label')),
                TextInput::make('view_all_url')
                    ->label(__('bricks.fields.view_all_url')),
            ]);
    }
}

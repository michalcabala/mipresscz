<?php

namespace App\Mason;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

class Stats extends Brick
{
    public static function getId(): string
    {
        return 'stats';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedChartBar;
    }

    public static function getLabel(): string
    {
        return __('bricks.stats');
    }

    /**
     * @throws Throwable
     */
    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.stats', [
            'heading' => $config['heading'] ?? null,
            'subheading' => $config['subheading'] ?? null,
            'items' => $config['items'] ?? [],
        ])->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                TextInput::make('heading')
                    ->label(__('bricks.fields.heading'))
                    ->columnSpanFull(),
                Textarea::make('subheading')
                    ->label(__('bricks.fields.subheading'))
                    ->rows(2)
                    ->columnSpanFull(),
                Repeater::make('items')
                    ->label(__('bricks.fields.items'))
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('value')
                            ->label(__('bricks.fields.value'))
                            ->required(),
                        TextInput::make('label')
                            ->label(__('bricks.fields.label'))
                            ->required(),
                        TextInput::make('description')
                            ->label(__('bricks.fields.description')),
                    ])
                    ->columns(1)
                    ->defaultItems(4)
                    ->reorderable()
                    ->collapsible(),
            ]);
    }
}

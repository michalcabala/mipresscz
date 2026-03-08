<?php

namespace App\Mason;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

class Heading extends Brick
{
    public static function getId(): string
    {
        return 'heading';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedH1;
    }

    public static function getLabel(): string
    {
        return __('bricks.heading');
    }

    /**
     * @throws Throwable
     */
    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.heading', [
            'text' => $config['text'] ?? null,
            'level' => $config['level'] ?? 'h2',
            'alignment' => $config['alignment'] ?? 'left',
        ])->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->schema([
                \Filament\Forms\Components\TextInput::make('text')
                    ->label(__('bricks.fields.text'))
                    ->required()
                    ->columnSpanFull(),
                \Filament\Forms\Components\Select::make('level')
                    ->label(__('bricks.fields.heading_level'))
                    ->options(['h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6'])
                    ->default('h2')
                    ->required(),
                \Filament\Forms\Components\Select::make('alignment')
                    ->label(__('bricks.fields.alignment'))
                    ->options([
                        'left' => __('bricks.alignment.left'),
                        'center' => __('bricks.alignment.center'),
                        'right' => __('bricks.alignment.right'),
                    ])
                    ->default('left'),
            ]);
    }
}

<?php

namespace App\Mason;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

class Button extends Brick
{
    public static function getId(): string
    {
        return 'button';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedCursorArrowRays;
    }

    public static function getLabel(): string
    {
        return __('bricks.button');
    }

    /**
     * @throws Throwable
     */
    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.button', [
            'label' => $config['label'] ?? null,
            'url' => $config['url'] ?? null,
            'variant' => $config['variant'] ?? 'primary',
            'alignment' => $config['alignment'] ?? 'left',
            'open_in_new_tab' => $config['open_in_new_tab'] ?? false,
        ])->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->schema([
                \Filament\Forms\Components\TextInput::make('label')
                    ->label(__('bricks.fields.button_label'))
                    ->required(),
                \Filament\Forms\Components\TextInput::make('url')
                    ->label(__('bricks.fields.button_url'))
                    ->url()
                    ->required(),
                \Filament\Forms\Components\Select::make('variant')
                    ->label(__('bricks.fields.button_variant'))
                    ->options([
                        'primary' => __('bricks.variants.primary'),
                        'secondary' => __('bricks.variants.secondary'),
                        'outline' => __('bricks.variants.outline'),
                    ])
                    ->default('primary'),
                \Filament\Forms\Components\Select::make('alignment')
                    ->label(__('bricks.fields.alignment'))
                    ->options([
                        'left' => __('bricks.alignment.left'),
                        'center' => __('bricks.alignment.center'),
                        'right' => __('bricks.alignment.right'),
                    ])
                    ->default('left'),
                \Filament\Forms\Components\Toggle::make('open_in_new_tab')
                    ->label(__('bricks.fields.open_in_new_tab'))
                    ->columnSpanFull(),
            ]);
    }
}

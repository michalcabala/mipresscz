<?php

namespace App\Mason;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

class Hero extends Brick
{
    public static function getId(): string
    {
        return 'hero';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedRectangleGroup;
    }

    public static function getLabel(): string
    {
        return __('bricks.hero');
    }

    /**
     * @throws Throwable
     */
    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.hero', [
            'heading' => $config['heading'] ?? null,
            'subheading' => $config['subheading'] ?? null,
            'button_label' => $config['button_label'] ?? null,
            'button_url' => $config['button_url'] ?? null,
            'media_id' => $config['media_id'] ?? null,
            'alignment' => $config['alignment'] ?? 'left',
        ])->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                \Filament\Forms\Components\TextInput::make('heading')
                    ->label(__('bricks.fields.heading'))
                    ->required()
                    ->columnSpanFull(),
                \Filament\Forms\Components\Textarea::make('subheading')
                    ->label(__('bricks.fields.subheading'))
                    ->rows(3)
                    ->columnSpanFull(),
                \Awcodes\Curator\Components\Forms\CuratorPicker::make('media_id')
                    ->label(__('bricks.fields.background_image'))
                    ->columnSpanFull(),
                \Filament\Forms\Components\TextInput::make('button_label')
                    ->label(__('bricks.fields.button_label')),
                \Filament\Forms\Components\TextInput::make('button_url')
                    ->label(__('bricks.fields.button_url'))
                    ->url(),
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

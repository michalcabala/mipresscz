<?php

namespace App\Mason;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
            'eyebrow' => $config['eyebrow'] ?? null,
            'heading' => $config['heading'] ?? null,
            'heading_highlight' => $config['heading_highlight'] ?? null,
            'subheading' => $config['subheading'] ?? null,
            'button_label' => $config['button_label'] ?? null,
            'button_url' => $config['button_url'] ?? null,
            'secondary_label' => $config['secondary_label'] ?? null,
            'secondary_url' => $config['secondary_url'] ?? null,
            'secondary_icon' => $config['secondary_icon'] ?? null,
            'media_id' => $config['media_id'] ?? null,
            'background' => $config['background'] ?? 'gradient',
            'alignment' => $config['alignment'] ?? 'left',
            'badges' => $config['badges'] ?? [],
            'fullscreen' => $config['fullscreen'] ?? true,
        ])->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                TextInput::make('eyebrow')
                    ->label(__('bricks.fields.eyebrow'))
                    ->placeholder('Laravel 12 + Filament 5')
                    ->columnSpanFull(),
                TextInput::make('heading')
                    ->label(__('bricks.fields.heading'))
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('heading_highlight')
                    ->label(__('bricks.fields.heading_highlight'))
                    ->helperText(__('bricks.fields.heading_highlight_hint'))
                    ->columnSpanFull(),
                Textarea::make('subheading')
                    ->label(__('bricks.fields.subheading'))
                    ->rows(3)
                    ->columnSpanFull(),
                Select::make('background')
                    ->label(__('bricks.fields.background'))
                    ->options([
                        'gradient' => __('bricks.backgrounds.gradient'),
                        'dark' => __('bricks.backgrounds.dark'),
                        'image' => __('bricks.backgrounds.image'),
                    ])
                    ->default('gradient'),
                CuratorPicker::make('media_id')
                    ->label(__('bricks.fields.background_image'))
                    ->columnSpanFull(),
                Select::make('fullscreen')
                    ->label(__('bricks.fields.fullscreen'))
                    ->options([
                        true => __('bricks.yes'),
                        false => __('bricks.no'),
                    ])
                    ->default(true),
                TextInput::make('button_label')
                    ->label(__('bricks.fields.button_label')),
                TextInput::make('button_url')
                    ->label(__('bricks.fields.button_url')),
                TextInput::make('secondary_label')
                    ->label(__('bricks.fields.secondary_label')),
                TextInput::make('secondary_url')
                    ->label(__('bricks.fields.secondary_url')),
                TextInput::make('secondary_icon')
                    ->label(__('bricks.fields.secondary_icon'))
                    ->placeholder('github'),
                Select::make('alignment')
                    ->label(__('bricks.fields.alignment'))
                    ->options([
                        'left' => __('bricks.alignment.left'),
                        'center' => __('bricks.alignment.center'),
                        'right' => __('bricks.alignment.right'),
                    ])
                    ->default('left'),
                Repeater::make('badges')
                    ->label(__('bricks.fields.badges'))
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('label')
                            ->label(__('bricks.fields.label'))
                            ->required(),
                    ])
                    ->columns(1)
                    ->defaultItems(0)
                    ->reorderable()
                    ->collapsible(),
            ]);
    }
}

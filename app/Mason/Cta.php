<?php

namespace App\Mason;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

class Cta extends Brick
{
    public static function getId(): string
    {
        return 'cta';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedCursorArrowRays;
    }

    public static function getLabel(): string
    {
        return __('bricks.cta');
    }

    /**
     * @throws Throwable
     */
    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.cta', [
            'heading' => $config['heading'] ?? null,
            'subheading' => $config['subheading'] ?? null,
            'button_label' => $config['button_label'] ?? null,
            'button_url' => $config['button_url'] ?? null,
            'secondary_label' => $config['secondary_label'] ?? null,
            'secondary_url' => $config['secondary_url'] ?? null,
            'variant' => $config['variant'] ?? 'blue',
        ])->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                TextInput::make('heading')
                    ->label(__('bricks.fields.heading'))
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('subheading')
                    ->label(__('bricks.fields.subheading'))
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('button_label')
                    ->label(__('bricks.fields.button_label')),
                TextInput::make('button_url')
                    ->label(__('bricks.fields.button_url'))
                    ->url(),
                TextInput::make('secondary_label')
                    ->label(__('bricks.fields.secondary_label')),
                TextInput::make('secondary_url')
                    ->label(__('bricks.fields.secondary_url'))
                    ->url(),
                Select::make('variant')
                    ->label(__('bricks.fields.variant'))
                    ->options([
                        'blue' => __('bricks.variants.blue'),
                        'dark' => __('bricks.variants.dark'),
                        'light' => __('bricks.variants.light'),
                    ])
                    ->default('blue'),
            ]);
    }
}

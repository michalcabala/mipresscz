<?php

namespace App\Mason;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

class Image extends Brick
{
    public static function getId(): string
    {
        return 'image';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedPhoto;
    }

    public static function getLabel(): string
    {
        return __('bricks.image');
    }

    /**
     * @throws Throwable
     */
    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.image', [
            'media_id' => $config['media_id'] ?? null,
            'caption' => $config['caption'] ?? null,
            'alt' => $config['alt'] ?? null,
        ])->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->schema([
                \Awcodes\Curator\Components\Forms\CuratorPicker::make('media_id')
                    ->label(__('bricks.fields.image'))
                    ->required()
                    ->columnSpanFull(),
                \Filament\Forms\Components\TextInput::make('caption')
                    ->label(__('bricks.fields.caption'))
                    ->columnSpanFull(),
                \Filament\Forms\Components\TextInput::make('alt')
                    ->label(__('bricks.fields.alt_text'))
                    ->columnSpanFull(),
            ]);
    }
}

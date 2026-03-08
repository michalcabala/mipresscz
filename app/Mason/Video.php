<?php

namespace App\Mason;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

class Video extends Brick
{
    public static function getId(): string
    {
        return 'video';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedPlayCircle;
    }

    public static function getLabel(): string
    {
        return __('bricks.video');
    }

    /**
     * @throws Throwable
     */
    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.video', [
            'url' => $config['url'] ?? null,
            'caption' => $config['caption'] ?? null,
        ])->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->schema([
                \Filament\Forms\Components\TextInput::make('url')
                    ->label(__('bricks.fields.video_url'))
                    ->url()
                    ->required()
                    ->placeholder('https://www.youtube.com/watch?v=...')
                    ->columnSpanFull(),
                \Filament\Forms\Components\TextInput::make('caption')
                    ->label(__('bricks.fields.caption'))
                    ->columnSpanFull(),
            ]);
    }
}

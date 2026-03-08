<?php

namespace App\Mason;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

class Gallery extends Brick
{
    public static function getId(): string
    {
        return 'gallery';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedSquares2x2;
    }

    public static function getLabel(): string
    {
        return __('bricks.gallery');
    }

    /**
     * @throws Throwable
     */
    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.gallery', [
            'media_ids' => $config['media_ids'] ?? [],
            'columns' => $config['columns'] ?? 3,
        ])->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->schema([
                \Awcodes\Curator\Components\Forms\CuratorPicker::make('media_ids')
                    ->label(__('bricks.fields.images'))
                    ->multiple()
                    ->required()
                    ->columnSpanFull(),
                \Filament\Forms\Components\Select::make('columns')
                    ->label(__('bricks.fields.columns'))
                    ->options([2 => '2', 3 => '3', 4 => '4'])
                    ->default(3),
            ]);
    }
}

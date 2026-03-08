<?php

namespace App\Mason;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

class Html extends Brick
{
    public static function getId(): string
    {
        return 'html';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedCodeBracket;
    }

    public static function getLabel(): string
    {
        return __('bricks.html');
    }

    /**
     * @throws Throwable
     */
    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.html', [
            'code' => $config['code'] ?? null,
        ])->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->schema([
                \Filament\Forms\Components\Textarea::make('code')
                    ->label(__('bricks.fields.html_code'))
                    ->required()
                    ->rows(10)
                    ->columnSpanFull(),
            ]);
    }
}

<?php

namespace App\Mason;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

class Divider extends Brick
{
    public static function getId(): string
    {
        return 'divider';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedMinus;
    }

    public static function getLabel(): string
    {
        return __('bricks.divider');
    }

    /**
     * @throws Throwable
     */
    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.divider', [
            'style' => $config['style'] ?? 'solid',
        ])->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action->modalHidden();
    }
}

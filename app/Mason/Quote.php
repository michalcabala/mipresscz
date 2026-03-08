<?php

namespace App\Mason;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

class Quote extends Brick
{
    public static function getId(): string
    {
        return 'quote';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedChatBubbleBottomCenterText;
    }

    public static function getLabel(): string
    {
        return __('bricks.quote');
    }

    /**
     * @throws Throwable
     */
    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.quote', [
            'text' => $config['text'] ?? null,
            'author' => $config['author'] ?? null,
            'source' => $config['source'] ?? null,
        ])->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->schema([
                \Filament\Forms\Components\Textarea::make('text')
                    ->label(__('bricks.fields.quote_text'))
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
                \Filament\Forms\Components\TextInput::make('author')
                    ->label(__('bricks.fields.author')),
                \Filament\Forms\Components\TextInput::make('source')
                    ->label(__('bricks.fields.source')),
            ]);
    }
}

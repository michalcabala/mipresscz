<?php

namespace App\Mason;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

class Text extends Brick
{
    public static function getId(): string
    {
        return 'text';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedDocumentText;
    }

    public static function getLabel(): string
    {
        return __('bricks.text');
    }

    /**
     * @throws Throwable
     */
    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.text', [
            'content' => $config['content'] ?? null,
        ])->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                RichEditor::make('content')
                    ->label(__('bricks.fields.content'))
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}

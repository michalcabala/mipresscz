<?php

namespace App\Mason;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

class Columns extends Brick
{
    public static function getId(): string
    {
        return 'columns';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedViewColumns;
    }

    public static function getLabel(): string
    {
        return __('bricks.columns');
    }

    /**
     * @throws Throwable
     */
    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.columns', [
            'left' => $config['left'] ?? null,
            'right' => $config['right'] ?? null,
            'ratio' => $config['ratio'] ?? '1:1',
        ])->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                \Filament\Forms\Components\RichEditor::make('left')
                    ->label(__('bricks.fields.left_column'))
                    ->columnSpanFull(),
                \Filament\Forms\Components\RichEditor::make('right')
                    ->label(__('bricks.fields.right_column'))
                    ->columnSpanFull(),
                \Filament\Forms\Components\Select::make('ratio')
                    ->label(__('bricks.fields.column_ratio'))
                    ->options(['1:1' => '1:1', '1:2' => '1:2', '2:1' => '2:1', '1:3' => '1:3', '3:1' => '3:1'])
                    ->default('1:1'),
            ]);
    }
}

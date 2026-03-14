<?php

namespace App\Mason;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

class Features extends Brick
{
    public static function getId(): string
    {
        return 'features';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedSquares2x2;
    }

    public static function getLabel(): string
    {
        return __('bricks.features');
    }

    /**
     * @throws Throwable
     */
    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.features', [
            'heading' => $config['heading'] ?? null,
            'subheading' => $config['subheading'] ?? null,
            'items' => $config['items'] ?? [],
            'columns' => $config['columns'] ?? '3',
        ])->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->slideOver()
            ->schema([
                TextInput::make('heading')
                    ->label(__('bricks.fields.heading'))
                    ->columnSpanFull(),
                Textarea::make('subheading')
                    ->label(__('bricks.fields.subheading'))
                    ->rows(2)
                    ->columnSpanFull(),
                Select::make('columns')
                    ->label(__('bricks.fields.columns'))
                    ->options([
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                    ])
                    ->default('3'),
                Repeater::make('items')
                    ->label(__('bricks.fields.items'))
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('icon')
                            ->label(__('bricks.fields.icon'))
                            ->placeholder('💡'),
                        TextInput::make('title')
                            ->label(__('bricks.fields.title'))
                            ->required(),
                        Textarea::make('description')
                            ->label(__('bricks.fields.description'))
                            ->rows(2),
                    ])
                    ->columns(1)
                    ->defaultItems(3)
                    ->reorderable()
                    ->collapsible(),
            ]);
    }
}

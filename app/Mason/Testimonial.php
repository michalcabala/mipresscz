<?php

namespace App\Mason;

use Awcodes\Mason\Brick;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

class Testimonial extends Brick
{
    public static function getId(): string
    {
        return 'testimonial';
    }

    public static function getIcon(): string|Heroicon|Htmlable|null
    {
        return Heroicon::OutlinedUserCircle;
    }

    public static function getLabel(): string
    {
        return __('bricks.testimonial');
    }

    /**
     * @throws Throwable
     */
    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.testimonial', [
            'quote' => $config['quote'] ?? null,
            'author' => $config['author'] ?? null,
            'company' => $config['company'] ?? null,
            'rating' => $config['rating'] ?? null,
            'media_id' => $config['media_id'] ?? null,
        ])->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action
            ->schema([
                \Filament\Forms\Components\Textarea::make('quote')
                    ->label(__('bricks.fields.quote_text'))
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
                \Filament\Forms\Components\TextInput::make('author')
                    ->label(__('bricks.fields.author'))
                    ->required(),
                \Filament\Forms\Components\TextInput::make('company')
                    ->label(__('bricks.fields.company')),
                \Filament\Forms\Components\TextInput::make('rating')
                    ->label(__('bricks.fields.rating'))
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5),
                \Awcodes\Curator\Components\Forms\CuratorPicker::make('media_id')
                    ->label(__('bricks.fields.author_photo'))
                    ->columnSpanFull(),
            ]);
    }
}

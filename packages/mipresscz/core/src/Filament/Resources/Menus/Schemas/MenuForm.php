<?php

namespace MiPressCz\Core\Filament\Resources\Menus\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('title')
                ->label(__('content.menu_fields.title'))
                ->required()
                ->maxLength(255),

            TextInput::make('handle')
                ->label(__('content.menu_fields.handle'))
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255)
                ->alphaDash()
                ->helperText('Unique identifier used in code (e.g. primary, footer).'),

            Select::make('location')
                ->label(__('content.menu_fields.location'))
                ->options([
                    'primary' => __('content.menu_locations.primary'),
                    'footer' => __('content.menu_locations.footer'),
                    'sidebar' => __('content.menu_locations.sidebar'),
                ])
                ->nullable()
                ->placeholder('—'),

            Textarea::make('description')
                ->label(__('content.menu_fields.description'))
                ->rows(2)
                ->maxLength(500)
                ->columnSpanFull(),
        ])->columns(2);
    }
}

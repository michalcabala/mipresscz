<?php

namespace MiPressCz\Core\Filament\Resources\MediaFolders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MediaFolderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('content.media_folder_fields.name'))
                    ->required()
                    ->maxLength(255),
                Select::make('parent_id')
                    ->label(__('content.media_folder_fields.parent'))
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
            ]);
    }
}

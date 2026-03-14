<?php

namespace MiPressCz\Core\Filament\Resources\MediaTags\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MediaTagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('content.media_tag_fields.name'))
                    ->required()
                    ->maxLength(255),
            ]);
    }
}

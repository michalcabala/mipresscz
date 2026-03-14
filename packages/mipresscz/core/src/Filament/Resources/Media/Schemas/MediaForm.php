<?php

namespace MiPressCz\Core\Filament\Resources\Media\Schemas;

use Awcodes\Curator\Resources\Media\Schemas\MediaForm as CuratorMediaForm;
use Filament\Forms\Components\Select;

class MediaForm extends CuratorMediaForm
{
    public static function getAdditionalInformationFormSchema(): array
    {
        return [
            ...parent::getAdditionalInformationFormSchema(),
            Select::make('media_folder_id')
                ->label(__('content.media_tag_fields.folder'))
                ->relationship('folder', 'name')
                ->searchable()
                ->preload()
                ->nullable(),
            Select::make('tags')
                ->label(__('content.media_tag_fields.tags'))
                ->relationship('tags', 'name')
                ->multiple()
                ->preload()
                ->searchable()
                ->createOptionForm([
                    \Filament\Forms\Components\TextInput::make('name')
                        ->label(__('content.media_tag_fields.name'))
                        ->required()
                        ->maxLength(255),
                    \Filament\Forms\Components\TextInput::make('slug')
                        ->label(__('content.media_tag_fields.slug'))
                        ->required()
                        ->maxLength(255),
                ]),
        ];
    }
}

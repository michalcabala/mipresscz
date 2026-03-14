<?php

namespace MiPressCz\Core\Filament\Resources\MediaTags\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MediaTagsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('content.media_tag_fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label(__('content.media_tag_fields.slug'))
                    ->sortable(),
                TextColumn::make('media_count')
                    ->label(__('content.media_tag_fields.media_count'))
                    ->counts('media')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('content.common.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name');
    }
}

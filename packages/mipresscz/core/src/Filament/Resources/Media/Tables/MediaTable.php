<?php

namespace MiPressCz\Core\Filament\Resources\Media\Tables;

use Awcodes\Curator\Resources\Media\Tables\MediaTable as CuratorMediaTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MediaTable extends CuratorMediaTable
{
    public static function configure(Table $table): Table
    {
        $table = parent::configure($table);

        return $table
            ->filters([
                SelectFilter::make('media_folder_id')
                    ->label(__('content.media_tag_fields.folder'))
                    ->relationship('folder', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('tags')
                    ->label(__('content.media_tag_fields.tags'))
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->preload(),
            ]);
    }

    public static function getDefaultTableColumns(): array
    {
        $columns = parent::getDefaultTableColumns();

        // Insert folder and tags columns after name (index 1)
        array_splice($columns, 2, 0, [
            TextColumn::make('folder.name')
                ->label(__('content.media_tag_fields.folder'))
                ->sortable()
                ->toggleable(),
            TextColumn::make('tags.name')
                ->label(__('content.media_tag_fields.tags'))
                ->badge()
                ->toggleable(),
        ]);

        return $columns;
    }
}

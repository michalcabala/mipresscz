<?php

namespace MiPressCz\Core\Filament\Resources\MediaFolders\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use MiPressCz\Core\Filament\Resources\MediaFolders\MediaFolderResource;
use Openplain\FilamentTreeView\Fields\TextField;
use Openplain\FilamentTreeView\Resources\Pages\TreePage;
use Openplain\FilamentTreeView\Tree;

class TreeMediaFolders extends TreePage
{
    protected static string $resource = MediaFolderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function tree(Tree $tree): Tree
    {
        return $tree
            ->fields([
                TextField::make('name')->weight(FontWeight::Medium),
            ])
            ->maxDepth(5)
            ->collapsed()
            ->autoSave()
            ->recordActions([
                EditAction::make()->url(fn ($record) => MediaFolderResource::getUrl('edit', ['record' => $record])),
                DeleteAction::make(),
            ]);
    }
}

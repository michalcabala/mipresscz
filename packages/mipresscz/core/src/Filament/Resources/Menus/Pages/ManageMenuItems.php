<?php

namespace MiPressCz\Core\Filament\Resources\Menus\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use MiPressCz\Core\Enums\MenuItemTarget;
use MiPressCz\Core\Enums\MenuItemType;
use MiPressCz\Core\Filament\Resources\Menus\MenuResource;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\Menu;
use Openplain\FilamentTreeView\Fields\IconField;
use Openplain\FilamentTreeView\Fields\TextField;
use Openplain\FilamentTreeView\Resources\Pages\TreeRelationPage;
use Openplain\FilamentTreeView\Tree;

class ManageMenuItems extends TreeRelationPage
{
    protected static string $resource = MenuResource::class;

    protected static string $relationship = 'items';

    protected static ?string $breadcrumb = null;

    public function getTitle(): string
    {
        /** @var Menu $owner */
        $owner = $this->getOwnerRecord();

        return __('content.menus.manage_items').' — '.$owner->title;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back_to_menu')
                ->label(__('content.menus.label'))
                ->icon('fal-arrow-left')
                ->color('gray')
                ->url(fn (): string => MenuResource::getUrl('edit', ['record' => $this->getOwnerRecord()])),

            CreateAction::make()
                ->label(__('content.menu_item_fields.type')),
        ];
    }

    public function tree(Tree $tree): Tree
    {
        return $tree
            ->fields([
                TextField::make('title')
                    ->weight(\Filament\Support\Enums\FontWeight::Medium)
                    ->dimWhenInactive(),
                IconField::make('is_active')->alignEnd(),
            ])
            ->maxDepth(5)
            ->autoSave()
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('type')
                ->label(__('content.menu_item_fields.type'))
                ->options(collect(MenuItemType::cases())->mapWithKeys(
                    fn (MenuItemType $type) => [$type->value => $type->label()]
                ))
                ->default(MenuItemType::CustomLink->value)
                ->required()
                ->live(),

            TextInput::make('title')
                ->label(__('content.menu_item_fields.title'))
                ->required()
                ->maxLength(255),

            TextInput::make('url')
                ->label(__('content.menu_item_fields.url'))
                ->url()
                ->maxLength(2048)
                ->visible(fn (Get $get): bool => $get('type') === MenuItemType::CustomLink->value),

            Select::make('entry_id')
                ->label(__('content.menu_item_fields.entry'))
                ->options(fn (): array => Entry::query()
                    ->orderBy('title')
                    ->pluck('title', 'id')
                    ->all()
                )
                ->searchable()
                ->nullable()
                ->visible(fn (Get $get): bool => $get('type') === MenuItemType::Entry->value),

            Select::make('target')
                ->label(__('content.menu_item_fields.target'))
                ->options(collect(MenuItemTarget::cases())->mapWithKeys(
                    fn (MenuItemTarget $target) => [$target->value => $target->label()]
                ))
                ->default(MenuItemTarget::Self->value)
                ->required(),

            Toggle::make('is_active')
                ->label(__('content.menu_item_fields.is_active'))
                ->default(true)
                ->columnSpanFull(),
        ]);
    }
}

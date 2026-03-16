<?php

namespace MiPressCz\Core\Filament\Resources\Entries\Pages;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use MiPressCz\Core\Filament\Resources\Entries\EntryResource;
use MiPressCz\Core\Models\Entry;

class EditEntrySeo extends EditRecord
{
    protected static string $resource = EntryResource::class;

    public static function getNavigationIcon(): Heroicon|string|null
    {
        return Heroicon::OutlinedMagnifyingGlass;
    }

    public static function getNavigationLabel(): string
    {
        return __('content.entry_fields.seo');
    }

    protected function resolveRecord(int|string $key): Entry
    {
        return Entry::with(['collection', 'metaOgImage'])->findOrFail($key);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('meta_title')
                ->label(__('content.entry_fields.meta_title'))
                ->helperText(__('content.entry_fields.meta_title_hint'))
                ->maxLength(255),
            Textarea::make('meta_description')
                ->label(__('content.entry_fields.meta_description'))
                ->helperText(__('content.entry_fields.meta_description_hint'))
                ->rows(3)
                ->maxLength(320),
            CuratorPicker::make('meta_og_image_id')
                ->label(__('content.entry_fields.meta_og_image'))
                ->relationship('metaOgImage', 'id')
                ->constrained(true)
                ->lazyLoad(true),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('content.actions.save'))
                ->color('gray')
                ->action(fn () => $this->save())
                ->keyBindings(['mod+s']),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }
}

<?php

namespace MiPressCz\Core\Filament\Pages;

use Filament\Actions\Action as TableAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use MiPressCz\Core\Models\Locale;

class ManageLocales extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'mipresscz-core::filament.pages.manage-locales';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('manage.locales') ?? false;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('locales.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('locales.navigation_label');
    }

    public function getTitle(): string
    {
        return __('locales.page_title');
    }

    public static function getNavigationSort(): ?int
    {
        return 10;
    }

    public static function getNavigationIcon(): \BackedEnum|string|null
    {
        return 'fal-language';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('locales.add_locale'))
                ->model(Locale::class)
                ->form($this->getLocaleForm())
                ->after(fn () => locales()->clearCache())
                ->successNotificationTitle(__('locales.created')),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Locale::query()->orderBy('order'))
            ->reorderable('order')
            ->columns([
                TextColumn::make('flag')
                    ->label('')
                    ->html()
                    ->state(fn (Locale $record): string => $record->flag
                        ? '<span class="inline-flex items-center justify-center w-7 h-7 rounded-full overflow-hidden"><img src="'.e(asset("assets/flags/{$record->flag}")).'" class="w-full h-full object-cover" /></span>'
                        : '<span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-700 text-xs font-bold">'.e(strtoupper(mb_substr($record->code, 0, 2))).'</span>')
                    ->width(40),

                TextColumn::make('code')
                    ->label(__('locales.fields.code'))
                    ->badge()
                    ->color('gray')
                    ->weight('bold'),

                TextColumn::make('native_name')
                    ->label(__('locales.fields.native_name'))
                    ->description(fn (Locale $record): string => $record->name),

                TextColumn::make('url_prefix')
                    ->label(__('locales.fields.url_prefix'))
                    ->placeholder(__('locales.no_prefix'))
                    ->badge()
                    ->color('info'),

                IconColumn::make('is_default')
                    ->label(__('locales.fields.is_default'))
                    ->boolean()
                    ->trueIcon(Heroicon::Star)
                    ->falseIcon(Heroicon::OutlinedStar)
                    ->trueColor('warning'),

                IconColumn::make('is_active')
                    ->label(__('locales.fields.is_active'))
                    ->boolean(),

                IconColumn::make('is_admin_available')
                    ->label(__('locales.fields.is_admin_available'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_frontend_available')
                    ->label(__('locales.fields.is_frontend_available'))
                    ->boolean(),
            ])
            ->actions([
                TableAction::make('set_default')
                    ->label(__('locales.set_as_default'))
                    ->icon(Heroicon::Star)
                    ->color('warning')
                    ->hidden(fn (Locale $record): bool => $record->is_default)
                    ->requiresConfirmation()
                    ->action(function (Locale $record): void {
                        Locale::query()->update(['is_default' => false]);
                        $record->update(['is_default' => true]);
                        locales()->clearCache();
                        Notification::make()
                            ->title(__('locales.set_as_default_success'))
                            ->success()
                            ->send();
                    }),

                EditAction::make()
                    ->form($this->getLocaleForm())
                    ->after(fn () => locales()->clearCache())
                    ->successNotificationTitle(__('locales.updated')),

                DeleteAction::make()
                    ->hidden(fn (Locale $record): bool => $record->is_default)
                    ->after(fn () => locales()->clearCache()),
            ]);
    }

    /** @return list<\Filament\Schemas\Components\Component> */
    protected function getLocaleForm(): array
    {
        return [
            Section::make(__('locales.form_sections.general'))
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('code')
                            ->label(__('locales.fields.code'))
                            ->required()
                            ->maxLength(10)
                            ->alphaDash()
                            ->unique(ignoreRecord: true),

                        TextInput::make('name')
                            ->label(__('locales.fields.name'))
                            ->required()
                            ->maxLength(100),

                        TextInput::make('native_name')
                            ->label(__('locales.fields.native_name'))
                            ->required()
                            ->maxLength(100),

                        Select::make('flag')
                            ->label(__('locales.fields.flag'))
                            ->options(function (): array {
                                $path = public_path('assets/flags');
                                if (! is_dir($path)) {
                                    return [];
                                }

                                return collect(scandir($path))
                                    ->filter(fn (string $file): bool => str_ends_with($file, '.svg'))
                                    ->sort()
                                    ->mapWithKeys(fn (string $file): array => [$file => pathinfo($file, PATHINFO_FILENAME)])
                                    ->all();
                            })
                            ->searchable()
                            ->nullable(),
                    ]),
                ]),

            Section::make(__('locales.form_sections.url'))
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('url_prefix')
                            ->label(__('locales.fields.url_prefix'))
                            ->maxLength(10)
                            ->nullable()
                            ->unique(ignoreRecord: true)
                            ->helperText(__('locales.url_prefix_help'))
                            ->hint(fn (): ?string => locales()->shouldPrefixUrls() ? null : __('locales.url_prefix_single_locale_hint'))
                            ->hintColor('warning'),

                        TextInput::make('fallback_locale')
                            ->label(__('locales.fields.fallback_locale'))
                            ->maxLength(10)
                            ->nullable(),

                        Select::make('direction')
                            ->label(__('locales.fields.direction'))
                            ->options(['ltr' => 'LTR', 'rtl' => 'RTL'])
                            ->default('ltr')
                            ->required(),
                    ]),

                    TextInput::make('date_format')
                        ->label(__('locales.fields.date_format'))
                        ->default('d.m.Y')
                        ->required()
                        ->maxLength(30),
                ]),

            Section::make(__('locales.form_sections.availability'))
                ->schema([
                    Grid::make(3)->schema([
                        Toggle::make('is_active')
                            ->label(__('locales.fields.is_active'))
                            ->default(true),

                        Toggle::make('is_admin_available')
                            ->label(__('locales.fields.is_admin_available'))
                            ->default(true),

                        Toggle::make('is_frontend_available')
                            ->label(__('locales.fields.is_frontend_available'))
                            ->default(true),
                    ]),
                ]),
        ];
    }
}

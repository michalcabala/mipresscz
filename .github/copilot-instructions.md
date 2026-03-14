# miPress CMS — Copilot Instructions

## Project Overview
miPress is a modular CMS built on Laravel 12 + Filament 5, designed as a professional WordPress alternative for the Czech market. It targets company presentations, small business websites, and community projects (fan clubs, restaurants, portfolios).

## Stack
- PHP 8.3.4, Laravel 12, Filament 5, Livewire 4, Tailwind CSS 4
- Pest 4 for testing
- Spatie Laravel Permission v7 (no Filament Shield)
- Served by Laravel Herd at `mipresscz.test`

## Installed Packages
- spatie/laravel-permission — roles and permissions
- bezhansalleh/filament-language-switch — language switching (cs/en)
- caresome/filament-auth-designer — auth page design
- awcodes/mason — block-based drag & drop page builder field for Filament
- awcodes/filament-curator — media picker/manager plugin for Filament (replaces spatie/laravel-medialibrary for Filament UI)
- openplain/filament-tree-view — drag & drop tree view page for hierarchical Filament resources
- codewithdennis/filament-select-tree — hierarchical select field for Filament forms & table filters

## Project Structure
- Filament panel: `admin` at `/mpcp` (AdminPanelProvider)
- Resources auto-discovered from `app/Filament/Resources/`
- Entry resources are dynamic per active collection via `EntryResourceConfiguration` and `AdminPanelProvider::getCollectionResources()`
- Locale management lives in `app/Filament/Pages/ManageLocales.php` as a Filament Page, not a Resource
- Frontend language switcher is a custom view component: `app/View/Components/LanguageSwitcher.php` + `resources/views/components/language-switcher.blade.php`
- Theme CSS entrypoint is `resources/css/filament/admin/theme.css` and already imports Mason + Curator plugin styles and `@source` paths
- Custom FontAwesome icon sets via blade-icons:
  - `fal-*` — FA Light (`resources/svg/fa/light/`)
  - `fab-*` — FA Brands (`resources/svg/fa/brands/`)
  - Prefix must NOT contain hyphens (blade-icons splits on first `-`)
- `README.md` is still the default Laravel scaffold and is not the source of truth for this project

## Architecture
- Content model: Collections → Blueprints → Entries (inspired by Statamic)
- Session driver: database
- Frontend routing uses both prefixed and unprefixed catch-all entry routes in `routes/web.php`
- Locale behavior is database-driven via `Locale`, `LocaleService`, and `SetFrontendLocale` middleware
- Single-language frontend mode omits locale prefixes and redirects prefixed URLs to unprefixed URLs
- Global locale access is centralized through the autoloaded `locales()` helper in `app/helpers.php`

## Roles & Permissions
- Enum `App\Enums\UserRole` (SuperAdmin, Admin, Editor, Contributor)
- SuperAdmin bypasses all permissions via `Gate::before()` in AppServiceProvider
- Seeder: `RolesAndPermissionsSeeder` (idempotent, safe to re-run)
- Translations: `lang/cs/roles.php`, `lang/en/roles.php`

## Providers
- `AppServiceProvider` — Gate::before, LanguageSwitch config
- `IconServiceProvider` — FilamentIcon aliases (dashboard icon etc.)
- `AdminPanelProvider` — Filament panel config

## Conventions

### Code
- Write clean, readable code with type hints
- Use PHP 8.3+ features (enums, readonly properties, named arguments, match expressions)
- Follow PSR-12 coding standard
- Models in `app/Models/`, enums in `app/Enums/`
- Filament resources in `app/Filament/Resources/`
- Follow the existing Filament split pattern: `Resource.php` delegates to `Schemas/*Form.php`, `Tables/*Table.php`, and `Pages/*`
- All UI strings must be translated via `__()` or `trans()`
- Database columns and code in English, UI text in Czech with English fallback
- Run `vendor/bin/pint --dirty --format agent` after modifying PHP files
- Use `php artisan make:*` commands to create new files
- Always pass `--no-interaction` to Artisan commands
- Filament icons: use `Heroicon` enum or blade-icons string (e.g. `fal-house`)
- Translations: Czech (`cs`) is primary, English (`en`) secondary
- Prefer `GlobalSet::findByHandle()` / `GlobalSet::getValue()` over inventing new global helpers
- Preserve existing render-hook based admin customizations in `AdminPanelProvider` unless the task explicitly changes panel chrome

### Testing
- Write corresponding tests after creating any new class, model, seeder, or service
- Tests go in `tests/Feature/` or `tests/Unit/` depending on nature
- Use Pest PHP (not PHPUnit syntax)
- Run each test and verify it passes: `php artisan test --filter=TestName`
- If a test fails, fix the code and re-run until it passes
- Test edge cases and validation
- Test suite uses MySQL database `mipresscz_testing` via `phpunit.xml`, not SQLite
- `tests/Pest.php` applies `RefreshDatabase` automatically to Feature tests
- Existing coverage includes content system, entry routing, locale workflow, locale observer, locale service, and roles/permissions

### Artisan Commands — run automatically after each relevant operation
- After migration change: `php artisan migrate`
- After seeder change: `php artisan db:seed --class=SeederName`
- After config change: `php artisan config:clear`
- After route change: `php artisan route:clear`
- After view change: `php artisan view:clear`
- After any major change: `php artisan optimize:clear`
- After Filament resource change: `php artisan filament:optimize-clear`
- After adding icons: `php artisan icons:clear && php artisan icons:cache`
- When in doubt: `php artisan optimize:clear`

### Git
- Commit after each logical unit with a descriptive message in English
- Format: `feat: add roles and permissions system`
- Types: feat, fix, refactor, test, chore, docs

### Documentation
- Treat application code and current docs in `docs/` as more reliable than `README.md`
- If a task changes core architecture, routing, locale behavior, or admin structure, update the relevant project documentation in `docs/`

## Workflow for Each Task
1. Read the assignment and make sure you understand it
2. Plan the implementation — which files to create/modify
3. Implement step by step
4. Run necessary artisan commands after each step
5. Write tests for new functionality
6. Run tests and verify they pass
7. If tests fail, fix and repeat
8. Commit with a descriptive message

## Mason (awcodes/mason)
Block-based drag & drop page/document builder field for Filament.

### Key Facts
- Data stored as JSON — cast model column to `array` or `json`, use `longText` DB column
- Bricks live in `App\Mason` namespace, views in `resources/views/mason/`
- Create bricks: `php artisan make:mason-brick BrickName`
- Config: `config/mason.php` (publish with `php artisan vendor:publish --tag="mason-config"`)
- Theme CSS must include Mason styles:
  ```css
  @import '../../../../vendor/awcodes/mason/resources/css/plugin.css';
  @source '../../../../vendor/awcodes/mason/resources/**/*.blade.php';
  ```

### Form Field Usage
```php
use Awcodes\Mason\Mason;

Mason::make('content')
    ->bricks([\App\Mason\Section::class])
    ->previewLayout('layouts.mason-preview') // app layout with @masonStyles
    ->doubleClickToEdit() // optional
    ->colorModeToggle() // optional light/dark
    ->sidebarPosition(SidebarPosition::Start) // optional
    ->sortBricks('asc') // optional
    ->displayActionsAsGrid() // optional
```

### Infolist Entry Usage
```php
use Awcodes\Mason\MasonEntry;

MasonEntry::make('content')
    ->bricks([\App\Mason\Section::class])
    ->previewLayout('layouts.mason-entry') // layout with @masonEntryStyles
```

### Rendering Content in Blade
```php
{!! mason(content: $post->content, bricks: BrickCollection::make())->toHtml() !!}
```

### Brick Class Structure
```php
use Awcodes\Mason\Brick;
use Filament\Actions\Action;

class Section extends Brick
{
    public static function getId(): string { return 'section'; }
    public static function getLabel(): string { return parent::getLabel(); }
    public static function getIcon(): string|Heroicon|Htmlable|null { return Heroicon::RectangleGroup; }

    public static function toHtml(array $config, ?array $data = null): ?string
    {
        return view('mason.section.index', $config)->render();
    }

    public static function configureBrickAction(Action $action): Action
    {
        return $action->slideOver()->schema([
            // Filament form components
        ]);
    }
}
```

### Faking Content in Tests
```php
use Awcodes\Mason\Support\Faker;

Faker::make()
    ->brick(id: 'section', config: ['text' => '<p>Test</p>'])
    ->asJson();
```

### Tips
- Use BrickCollection classes to avoid repeating brick arrays
- For static bricks (no form): return `$action->modalHidden()` in `configureBrickAction`
- Custom height: `->extraInputAttributes(['style' => 'min-height: 30rem;'])`
- Auth guard config: set `routes.middleware` in `config/mason.php`

## Filament Curator (awcodes/filament-curator)
Media picker/manager plugin for Filament Panels.

### Key Facts
- WARNING: Does NOT work with Spatie Media Library
- Install: `composer require awcodes/filament-curator` then `php artisan curator:install`
- Config: `config/curator.php` (publish with `php artisan vendor:publish --tag="curator-config"`)
- Theme CSS must include Curator styles:
  ```css
  @import '../../../../vendor/awcodes/filament-curator/resources/css/plugin.css';
  @source '../../../../vendor/awcodes/filament-curator/resources/**/*.blade.php';
  ```

### Panel Registration
```php
use Awcodes\Curator\CuratorPlugin;

public function panel(Panel $panel): Panel
{
    return $panel->plugins([
        CuratorPlugin::make()
            ->label('Media')
            ->pluralLabel('Media')
            ->navigationIcon(Heroicon::OutlinedPhoto)
            ->navigationGroup('Content')
            ->navigationSort(3)
            ->showBadge(true)
            ->registerNavigation(true)
            ->curations(true)
            ->fileSwap(true),
    ]);
}
```

### CuratorPicker Form Field
```php
use Awcodes\Curator\Components\Forms\CuratorPicker;

CuratorPicker::make('featured_image_id')
    ->label('Featured Image')
    ->relationship('featured_image', 'id') // single
    ->constrained(true)
    ->lazyLoad(true)
    ->pathGenerator(DatePathGenerator::class) // optional
    ->visibility('public') // if public access needed
```

Multiple images:
```php
CuratorPicker::make('gallery_ids')
    ->multiple()
    ->relationship('gallery', 'id')
    ->orderColumn('order')
```

### CuratorColumn Table Column
```php
use Awcodes\Curator\Components\Tables\CuratorColumn;

CuratorColumn::make('featured_image')->size(40)

// Multiple with stacking
CuratorColumn::make('gallery')->ring(2)->overlap(4)->limit(3)
```

### Model Relationships
```php
use Awcodes\Curator\Models\Media;

// Single image
public function featuredImage(): BelongsTo
{
    return $this->belongsTo(Media::class, 'featured_image_id', 'id');
}

// Multiple images
public function gallery(): BelongsToMany
{
    return $this->belongsToMany(Media::class, 'media_post', 'post_id', 'media_id')
        ->withPivot('order')->orderBy('order');
}
```

### Eager Loading (prevent N+1)
```php
protected function getTableQuery(): Builder
{
    return parent::getTableQuery()->with(['featured_image', 'gallery']);
}
```

### RichEditor Integration
```php
use Awcodes\Curator\Components\Forms\RichEditor\AttachCuratorMediaPlugin;

RichEditor::make('content')
    ->tools(['attachCuratorMedia'])
    ->plugins([AttachCuratorMediaPlugin::make()])
```

### Path Generators
- `DefaultPathGenerator` — saves in disk/directory
- `DatePathGenerator` — saves in disk/directory/Y/m/d
- `UserPathGenerator` — saves in disk/directory/user-auth-identifier
- Custom: implement `PathGenerator` interface

### Glider Blade Component
```blade
<x-curator-glider :media="$mediaId" width="800" height="600" format="webp" quality="80" />
```

### Curation Presets
```php
use Awcodes\Curator\Curations\CurationPreset;
use Awcodes\Curator\Facades\Curation;

Curation::presets([
    CurationPreset::make('Thumbnail')->height(200)->width(200)->format('webp')->quality(80),
]);
```

### Curation Blade Component
```blade
<x-curator-curation :media="$media" curation="thumbnail" loading="lazy" />
```

### Custom Media Model
```php
use Awcodes\Curator\Models\Media;

class CustomMedia extends Media
{
    protected $table = 'media';
}
// Set in config: 'model' => \App\Models\CustomMedia::class
```

## Filament Tree View (openplain/filament-tree-view)
Drag & drop tree view page for hierarchical Filament resources. Replaces the list page with an interactive tree.

### Key Facts
- Requires `parent_id` (nullable FK) and `order` (integer) columns on the model
- Model must use `Openplain\FilamentTreeView\Concerns\HasTreeStructure` trait
- Tree page extends `Openplain\FilamentTreeView\Resources\Pages\TreePage`
- Relation page extends `Openplain\FilamentTreeView\Resources\Pages\TreeRelationPage`
- Supports UUID primary keys out of the box (keep `parent_id` nullable, do NOT override `getParentKeyDefaultValue()`)
- After install: `php artisan filament:assets`

### Model Setup
```php
use Illuminate\Database\Eloquent\Model;
use Openplain\FilamentTreeView\Concerns\HasTreeStructure;

class Category extends Model
{
    use HasTreeStructure;

    protected $fillable = ['name', 'parent_id', 'order', 'is_active'];
}
```

### Resource tree() Method
```php
use Openplain\FilamentTreeView\Fields\IconField;
use Openplain\FilamentTreeView\Fields\TextField;
use Openplain\FilamentTreeView\Tree;

public static function tree(Tree $tree): Tree
{
    return $tree
        ->fields([
            TextField::make('name')->weight(FontWeight::Medium)->dimWhenInactive(),
            IconField::make('is_active')->alignEnd(),
        ])
        ->maxDepth(5)           // limit nesting (default 10, null = unlimited)
        ->collapsed()           // start collapsed
        ->autoSave()            // save on drag (default: manual save)
        ->recordActions([
            EditAction::make()->url(fn ($record) => static::getUrl('edit', ['record' => $record])),
            DeleteAction::make(),
        ]);
}
```

### Tree Page
```php
use Openplain\FilamentTreeView\Resources\Pages\TreePage;

class TreeCategories extends TreePage
{
    protected static string $resource = CategoryResource::class;
}
```

Register in resource: `'index' => Pages\TreeCategories::route('/'),`

### Query Customization
```php
->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'active'))
```

### TextField Options
```php
TextField::make('name')
    ->size('sm' | 'base' | 'lg')
    ->weight(FontWeight::Medium)
    ->color('primary' | 'gray' | 'success')
    ->limit(50)
    ->formatStateUsing(fn (string $state) => strtoupper($state))
    ->dimWhenInactive()         // dims when is_active = false
    ->dimWhenInactive('custom') // custom field
    ->alignEnd();
```

### Translatable Support
Use `Openplain\FilamentTreeView\Resources\Concerns\Translatable` on Resource and `Openplain\FilamentTreeView\Resources\Pages\TreePage\Concerns\Translatable` on TreePage for Spatie Translatable integration.

## Filament Select Tree (codewithdennis/filament-select-tree)
Hierarchical multi-level select field for Filament forms and table filters.

### Key Facts
- Requires `parent_id` column on the related model
- After install: `php artisan filament:assets`
- Works with `BelongsTo` (single) and `BelongsToMany` (multiple) relationships
- Can also be used without relationships via `->query()`

### BelongsToMany Relationship
```php
use CodeWithDennis\FilamentSelectTree\SelectTree;

SelectTree::make('categories')
    ->relationship('categories', 'name', 'parent_id')
```

### BelongsTo Relationship
```php
SelectTree::make('category_id')
    ->relationship('category', 'name', 'parent_id')
```

### Without Relationship
```php
SelectTree::make('category_id')
    ->query(fn() => Category::query(), 'name', 'parent_id')
```

### Key Methods
```php
SelectTree::make('categories')
    ->relationship('categories', 'name', 'parent_id')
    ->placeholder(__('Select category'))     // custom placeholder
    ->enableBranchNode()                     // allow selecting parent nodes
    ->withCount()                            // show children count
    ->alwaysOpen()                           // keep dropdown open
    ->staticList()                           // render as static DOM (no overlap)
    ->independent(false)                     // set nodes as dependent
    ->expandSelected(false)                  // expand tree to selected values
    ->defaultOpenLevel(2)                    // expand to this level
    ->searchable()                           // enable search
    ->clearable(false)                       // hide clear icon
    ->grouped(false)                         // show leaves instead of groups
    ->showTags(false)                        // show count instead of tags
    ->tagsCountText('selected')              // custom count text
    ->disabledOptions([2, 3])                // disable specific options
    ->hiddenOptions([4, 5])                  // hide specific options
    ->withTrashed()                          // include soft-deleted
    ->multiple(false)                        // force single select
    ->direction('top')                       // force direction: auto|top|bottom
    ->parentNullValue(-1);                   // custom null value for root
```

### Custom Queries
```php
SelectTree::make('categories')
    ->relationship(
        relationship: 'categories',
        titleAttribute: 'name',
        parentAttribute: 'parent_id',
        modifyQueryUsing: fn($query) => $query->where('is_active', true),
        modifyChildQueryUsing: fn($query) => $query->orderBy('name'),
    )
```

### Table Filter Usage
```php
use Filament\Tables\Filters\Filter;
use CodeWithDennis\FilamentSelectTree\SelectTree;

Filter::make('tree')
    ->form([
        SelectTree::make('categories')
            ->relationship('categories', 'name', 'parent_id')
            ->independent(false)
            ->enableBranchNode(),
    ])
    ->query(function (Builder $query, array $data) {
        return $query->when($data['categories'], function ($query, $categories) {
            return $query->whereHas('categories', fn($q) => $q->whereIn('id', $categories));
        });
    })
```

## Do NOT
- Install new composer/npm packages without explicit instruction
- Modify Filament panel config (AdminPanelProvider) without instruction
- Modify existing migrations — create new ones instead
- Use Filament Shield — we have a custom role system
- Publish vendor views unless absolutely necessary
- Write "TODO" or "FIXME" comments — complete everything immediately

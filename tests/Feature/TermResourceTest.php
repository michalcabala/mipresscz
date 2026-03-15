<?php

use App\Enums\UserRole;
use App\Models\User;
use App\Providers\Filament\AdminPanelProvider;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use MiPressCz\Core\Filament\Resources\Terms\Pages\CreateTerm;
use MiPressCz\Core\Filament\Resources\Terms\Pages\ListTerms;
use MiPressCz\Core\Filament\Resources\Terms\TermResource;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Locale;
use MiPressCz\Core\Models\Taxonomy;
use MiPressCz\Core\Models\Term;
use ReflectionMethod;

beforeEach(function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    (new \Database\Seeders\RolesAndPermissionsSeeder)->run();

    Locale::factory()->create([
        'code' => 'cs',
        'is_default' => true,
        'is_active' => true,
        'order' => 1,
    ]);
    locales()->clearCache();

    $this->admin = User::factory()->create(['role' => UserRole::Admin]);
    $this->admin->syncRoles([UserRole::Admin->value]);
    $this->actingAs($this->admin);

    $this->collection = Collection::factory()->create([
        'handle' => 'articles',
        'title' => 'Články',
        'is_active' => true,
    ]);
});

it('shows only scoped taxonomy terms and hides taxonomy filter in scoped term resources', function () {
    $taxonomy = Taxonomy::factory()->create([
        'title' => 'Kategorie',
        'handle' => 'categories',
    ]);
    $otherTaxonomy = Taxonomy::factory()->create([
        'title' => 'Štítky',
        'handle' => 'tags',
    ]);
    $this->collection->taxonomies()->attach([$taxonomy->id, $otherTaxonomy->id]);

    $matchingTerm = Term::factory()->create([
        'taxonomy_id' => $taxonomy->id,
        'title' => 'Laravel',
        'locale' => 'cs',
    ]);
    $otherTerm = Term::factory()->create([
        'taxonomy_id' => $otherTaxonomy->id,
        'title' => 'PHP',
        'locale' => 'cs',
    ]);

    $configurationKey = 'articles-categories';
    $panel = Filament::getDefaultPanel();
    Filament::setCurrentPanel($panel);
    Route::get("/_test/terms/{$configurationKey}", fn () => 'ok')
        ->name("filament.admin.resources.terms.{$configurationKey}.index");
    Route::get("/_test/terms/{$configurationKey}/create", fn () => 'ok')
        ->name("filament.admin.resources.terms.{$configurationKey}.create");
    Route::get("/_test/terms/{$configurationKey}/{record}/edit", fn () => 'ok')
        ->name("filament.admin.resources.terms.{$configurationKey}.edit");

    $panel->resources([
        TermResource::make($configurationKey)
            ->slug("terms/{$configurationKey}")
            ->taxonomyHandle($taxonomy->handle)
            ->navigationLabel($taxonomy->title)
            ->navigationParentItem($this->collection->title),
    ]);

    TermResource::withConfiguration($configurationKey, fn () => Livewire::test(ListTerms::class)
        ->call('loadTable')
        ->assertCanSeeTableRecords([$matchingTerm])
        ->assertCanNotSeeTableRecords([$otherTerm])
        ->assertTableColumnHidden('taxonomy.title')
        ->assertTableFilterHidden('taxonomy_id')
        ->assertTableFilterHidden('locale')
    );
});

it('prefills taxonomy on scoped term create page', function () {
    $taxonomy = Taxonomy::factory()->create([
        'title' => 'Kategorie',
        'handle' => 'categories',
    ]);
    $this->collection->taxonomies()->attach($taxonomy);

    $configurationKey = 'articles-categories-create';
    $panel = Filament::getDefaultPanel();
    Filament::setCurrentPanel($panel);
    Route::get("/_test/terms/{$configurationKey}", fn () => 'ok')
        ->name("filament.admin.resources.terms.{$configurationKey}.index");
    Route::get("/_test/terms/{$configurationKey}/create", fn () => 'ok')
        ->name("filament.admin.resources.terms.{$configurationKey}.create");
    Route::get("/_test/terms/{$configurationKey}/{record}/edit", fn () => 'ok')
        ->name("filament.admin.resources.terms.{$configurationKey}.edit");

    $panel->resources([
        TermResource::make($configurationKey)
            ->slug("terms/{$configurationKey}")
            ->taxonomyHandle($taxonomy->handle)
            ->navigationLabel($taxonomy->title)
            ->navigationParentItem($this->collection->title),
    ]);

    TermResource::withConfiguration($configurationKey, fn () => Livewire::test(CreateTerm::class)
        ->assertFormSet([
            'taxonomy_id' => $taxonomy->getKey(),
            'locale' => 'cs',
        ])
        ->fillForm([
            'title' => 'Nový termín',
            'slug' => 'novy-termin',
            'locale' => 'cs',
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
    );

    expect(Term::query()
        ->where('slug', 'novy-termin')
        ->where('taxonomy_id', $taxonomy->getKey())
        ->exists())->toBeTrue();
});

it('registers taxonomy navigation under its assigned collection', function () {
    $taxonomy = Taxonomy::factory()->create([
        'title' => 'Kategorie',
        'handle' => 'categories',
        'is_active' => true,
    ]);

    $this->collection->taxonomies()->attach($taxonomy);

    $provider = new AdminPanelProvider(app());
    $method = new ReflectionMethod($provider, 'getTaxonomyNavigationItems');
    $method->setAccessible(true);

    $navigationItems = $method->invoke($provider);

    expect($navigationItems)->toHaveCount(1)
        ->and($navigationItems[0]->getLabel())->toBe('Kategorie')
        ->and($navigationItems[0]->getParentItem())->toBe('Články');
});

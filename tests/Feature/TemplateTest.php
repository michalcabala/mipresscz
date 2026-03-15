<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use MiPressCz\Core\Models\Setting;
use MiPressCz\Core\Services\TemplateManager;

beforeEach(function (): void {
    Cache::flush();
});

// ---------------------------------------------------------------------------
// Setting model
// ---------------------------------------------------------------------------

it('stores and retrieves a setting by key', function (): void {
    Setting::set('test_key', 'test_value');

    expect(Setting::get('test_key'))->toBe('test_value');
});

it('returns the default value when a setting is missing', function (): void {
    expect(Setting::get('nonexistent_key', 'fallback'))->toBe('fallback');
});

it('overwrites an existing setting', function (): void {
    Setting::set('overwrite_key', 'first');
    Setting::set('overwrite_key', 'second');

    Cache::flush();

    expect(Setting::get('overwrite_key'))->toBe('second');
});

it('invalidates cache on save', function (): void {
    Setting::set('cache_key', 'original');

    // Prime the cache
    expect(Setting::get('cache_key'))->toBe('original');

    // Update via updateOrCreate — triggers saved event
    Setting::set('cache_key', 'updated');

    // Cache should be cleared; fresh DB read returns new value
    Cache::flush();
    expect(Setting::get('cache_key'))->toBe('updated');
});

// ---------------------------------------------------------------------------
// TemplateManager::getAvailable()
// ---------------------------------------------------------------------------

it('returns at least the default template', function (): void {
    $templates = app(TemplateManager::class)->getAvailable();

    expect($templates)->not->toBeEmpty()
        ->and($templates->pluck('slug'))->toContain('default');
});

it('returns template metadata from template.json', function (): void {
    $templates = app(TemplateManager::class)->getAvailable();
    $default = $templates->firstWhere('slug', 'default');

    expect($default)->not->toBeNull()
        ->and($default['name'])->toBe('Default')
        ->and($default['author'])->toBe('miPress')
        ->and($default['version'])->toBe('1.0.0');
});

it('ignores directories without template.json', function (): void {
    $manager = app(TemplateManager::class);

    // Create a directory without template.json
    $fakePath = resource_path('views/templates/_no_json_'.uniqid());
    mkdir($fakePath, 0755, true);

    $templates = $manager->getAvailable();

    expect($templates->pluck('slug'))->not->toContain('_no_json_');

    rmdir($fakePath);
});

// ---------------------------------------------------------------------------
// TemplateManager::getActive()
// ---------------------------------------------------------------------------

it('returns default when no setting exists', function (): void {
    Cache::flush();
    Setting::query()->where('key', 'active_template')->delete();

    expect(app(TemplateManager::class)->getActive())->toBe('default');
});

it('returns the stored active template slug', function (): void {
    Setting::set('active_template', 'default');
    Cache::flush();

    expect(app(TemplateManager::class)->getActive())->toBe('default');
});

it('memoizes the active template within the current request', function (): void {
    Setting::set('active_template', 'default');
    Cache::flush();

    app()->forgetInstance(TemplateManager::class);
    $manager = app(TemplateManager::class);

    $queries = [];

    DB::listen(static function ($query) use (&$queries): void {
        $queries[] = $query->sql;
    });

    // First call — may trigger DB/cache queries
    expect($manager->getActive())->toBe('default');
    $queriesAfterFirst = count($queries);

    // Second call — should be memoized, zero additional queries
    expect($manager->getActive())->toBe('default');
    $queriesAfterSecond = count($queries);

    expect($queriesAfterSecond - $queriesAfterFirst)->toBe(0);
});

// ---------------------------------------------------------------------------
// TemplateManager::setActive()
// ---------------------------------------------------------------------------

it('persists the active template in the settings table', function (): void {
    app(TemplateManager::class)->setActive('default');

    Cache::flush();

    expect(Setting::find('active_template'))->not->toBeNull()
        ->and(Setting::find('active_template')->value)->toBe('default');
});

it('throws an exception when activating a non-existent template', function (): void {
    expect(fn () => app(TemplateManager::class)->setActive('nonexistent_xyz'))
        ->toThrow(\InvalidArgumentException::class);
});

// ---------------------------------------------------------------------------
// TemplateManager::getPath()
// ---------------------------------------------------------------------------

it('returns the correct absolute path for a template slug', function (): void {
    $path = app(TemplateManager::class)->getPath('default');

    expect($path)->toEndWith('views/templates/default')
        ->and(is_dir($path))->toBeTrue();
});

// ---------------------------------------------------------------------------
// TemplateManager::registerViewNamespace()
// ---------------------------------------------------------------------------

it('registers the template blade namespace', function (): void {
    $manager = app(TemplateManager::class);
    $manager->registerViewNamespace();

    // The namespace should resolve existing views
    expect(view()->exists('template::layouts.app'))->toBeTrue()
        ->and(view()->exists('template::pages.home'))->toBeTrue()
        ->and(view()->exists('template::pages.page'))->toBeTrue();
});

it('falls back to default views when active template view is missing', function (): void {
    // Even if active is something non-existent, default views should still resolve
    Setting::set('active_template', 'default');
    Cache::flush();

    app(TemplateManager::class)->registerViewNamespace();

    expect(view()->exists('template::layouts.app'))->toBeTrue();
});

// ---------------------------------------------------------------------------
// active_template() helper
// ---------------------------------------------------------------------------

it('active_template helper returns the active slug', function (): void {
    Setting::set('active_template', 'default');
    Cache::flush();

    expect(active_template())->toBe('default');
});

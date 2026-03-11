<?php

use Illuminate\Support\Facades\Cache;
use MiPressCz\Core\Models\Locale;
use MiPressCz\Core\Services\LocaleService;

beforeEach(function () {
    locales()->clearCache();
});

it('returns all locales ordered', function () {
    Locale::factory()->create(['code' => 'de', 'order' => 5, 'is_active' => true]);
    Locale::factory()->create(['code' => 'fr', 'order' => 1, 'is_active' => true]);

    $codes = locales()->getAll()->pluck('code')->all();

    expect($codes[0])->toBe('fr');
});

it('returns only active locales', function () {
    Locale::factory()->create(['code' => 'de', 'is_active' => true, 'order' => 1]);
    Locale::factory()->create(['code' => 'fr', 'is_active' => false, 'order' => 2]);

    expect(locales()->getActive()->pluck('code')->all())->toContain('de');
    expect(locales()->getActive()->pluck('code')->all())->not->toContain('fr');
});

it('returns default locale', function () {
    Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'is_active' => true, 'order' => 1]);
    Locale::factory()->create(['code' => 'en', 'is_default' => false, 'is_active' => true, 'order' => 2]);

    expect(locales()->getDefault()?->code)->toBe('cs');
    expect(locales()->getDefaultCode())->toBe('cs');
});

it('finds locale by code', function () {
    Locale::factory()->create(['code' => 'cs', 'order' => 1]);

    expect(locales()->findByCode('cs'))->not->toBeNull();
    expect(locales()->findByCode('xx'))->toBeNull();
});

it('returns active codes as array', function () {
    Locale::factory()->create(['code' => 'cs', 'is_active' => true, 'order' => 1]);
    Locale::factory()->create(['code' => 'en', 'is_active' => true, 'order' => 2]);
    Locale::factory()->create(['code' => 'de', 'is_active' => false, 'order' => 3]);

    $codes = locales()->getActiveCodes();

    expect($codes)->toContain('cs')->toContain('en')->not->toContain('de');
});

it('detects multilingual site', function () {
    Locale::factory()->create(['code' => 'cs', 'is_active' => true, 'order' => 1]);

    expect(locales()->isMultilingual())->toBeFalse();

    Locale::factory()->create(['code' => 'en', 'is_active' => true, 'order' => 2]);
    locales()->clearCache();

    expect(locales()->isMultilingual())->toBeTrue();
});

it('shouldPrefixUrls returns false for single frontend locale', function () {
    Locale::factory()->create(['code' => 'cs', 'is_active' => true, 'is_frontend_available' => true, 'order' => 1]);

    expect(locales()->shouldPrefixUrls())->toBeFalse();
});

it('shouldPrefixUrls returns true for multiple frontend locales', function () {
    Locale::factory()->create(['code' => 'cs', 'is_active' => true, 'is_frontend_available' => true, 'order' => 1]);
    Locale::factory()->create(['code' => 'en', 'is_active' => true, 'is_frontend_available' => true, 'order' => 2]);

    expect(locales()->shouldPrefixUrls())->toBeTrue();
});

it('shouldPrefixUrls ignores non-frontend locales', function () {
    Locale::factory()->create(['code' => 'cs', 'is_active' => true, 'is_frontend_available' => true, 'order' => 1]);
    Locale::factory()->create(['code' => 'en', 'is_active' => true, 'is_frontend_available' => false, 'order' => 2]);

    expect(locales()->shouldPrefixUrls())->toBeFalse();
});

it('caches locales', function () {
    Locale::factory()->create(['code' => 'cs', 'is_active' => true, 'order' => 1]);

    locales()->getAll();

    expect(Cache::has(LocaleService::CACHE_KEY))->toBeTrue();
});

it('clears cache', function () {
    Locale::factory()->create(['code' => 'cs', 'is_active' => true, 'order' => 1]);
    locales()->getAll();

    locales()->clearCache();

    expect(Cache::has(LocaleService::CACHE_KEY))->toBeFalse();
});

it('returns language switch config', function () {
    Locale::factory()->create([
        'code' => 'cs',
        'native_name' => 'Čeština',
        'flag' => 'CZ.svg',
        'is_active' => true,
        'is_admin_available' => true,
        'order' => 1,
    ]);

    $config = locales()->toLanguageSwitchConfig();

    expect($config)->toHaveKeys(['locales', 'labels', 'flags']);
    expect($config['locales'])->toContain('cs');
    expect($config['labels']['cs'])->toBe('Čeština');
    expect($config['flags']['cs'])->toContain('CZ.svg');
});

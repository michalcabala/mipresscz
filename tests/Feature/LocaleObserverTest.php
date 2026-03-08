<?php

use App\Models\Locale;
use App\Services\LocaleService;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    locales()->clearCache();
});

it('clears cache when locale is created', function () {
    Locale::factory()->create(['code' => 'cs', 'order' => 1]);
    locales()->getAll(); // populate cache

    Locale::factory()->create(['code' => 'en', 'order' => 2]);

    expect(Cache::has(LocaleService::CACHE_KEY))->toBeFalse();
});

it('clears cache when locale is updated', function () {
    $locale = Locale::factory()->create(['code' => 'cs', 'order' => 1]);
    locales()->getAll(); // populate cache

    $locale->update(['name' => 'Czech updated']);

    expect(Cache::has(LocaleService::CACHE_KEY))->toBeFalse();
});

it('clears cache when locale is deleted', function () {
    $locale = Locale::factory()->create(['code' => 'cs', 'order' => 1]);
    locales()->getAll(); // populate cache

    $locale->delete();

    expect(Cache::has(LocaleService::CACHE_KEY))->toBeFalse();
});

it('ensures only one default locale when setting default', function () {
    $cs = Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'order' => 1]);
    $en = Locale::factory()->create(['code' => 'en', 'is_default' => false, 'order' => 2]);

    $en->update(['is_default' => true]);

    expect($cs->fresh()->is_default)->toBeFalse();
    expect($en->fresh()->is_default)->toBeTrue();
});

<?php

use Illuminate\Support\Str;
use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;

beforeEach(function () {
    $this->collection = Collection::factory()->create([
        'handle' => 'pages',
        'route_template' => '/{slug}',
    ]);
    $this->blueprint = Blueprint::factory()->create([
        'collection_id' => $this->collection->id,
    ]);
});

it('generates a preview token on the entry', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
    ]);

    $token = $entry->generatePreviewToken();

    expect($token)->toHaveLength(64);
    expect($entry->fresh()->preview_token)->toBe($token);
    expect($entry->fresh()->preview_token_expires_at)->toBeInstanceOf(\Carbon\Carbon::class);
});

it('validates a correct preview token', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
    ]);

    $token = $entry->generatePreviewToken();

    expect($entry->isPreviewTokenValid($token))->toBeTrue();
});

it('rejects an incorrect preview token', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
    ]);

    $entry->generatePreviewToken();

    expect($entry->isPreviewTokenValid('wrong-token'))->toBeFalse();
});

it('rejects an expired preview token', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
    ]);

    $token = $entry->generatePreviewToken();

    // Manually expire the token
    $entry->update(['preview_token_expires_at' => now()->subHour()]);

    expect($entry->fresh()->isPreviewTokenValid($token))->toBeFalse();
});

it('shows a draft entry via preview token', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'title' => 'Tajný koncept',
        'slug' => 'tajny-koncept',
        'status' => EntryStatus::Draft,
        'locale' => 'cs',
    ]);

    $token = $entry->generatePreviewToken();

    $response = $this->get("/_preview/{$token}");

    $response->assertSuccessful();
    $response->assertSee('Tajný koncept');
});

it('returns 404 for invalid preview token', function () {
    $fakeToken = Str::random(64);

    $this->get("/_preview/{$fakeToken}")->assertNotFound();
});

it('returns 404 for expired preview token on route', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'title' => 'Starý náhled',
        'slug' => 'stary-nahled',
        'status' => EntryStatus::Draft,
        'locale' => 'cs',
    ]);

    $token = $entry->generatePreviewToken();
    $entry->update(['preview_token_expires_at' => now()->subHour()]);

    $this->get("/_preview/{$token}")->assertNotFound();
});

it('generates a valid preview URL', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
    ]);

    $url = $entry->getPreviewUrl();

    expect($url)->toContain('/_preview/');
    expect($entry->fresh()->preview_token)->not->toBeNull();
});

it('reuses existing valid preview token in getPreviewUrl', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
    ]);

    $url1 = $entry->getPreviewUrl();
    $token1 = $entry->fresh()->preview_token;

    $url2 = $entry->getPreviewUrl();
    $token2 = $entry->fresh()->preview_token;

    expect($token1)->toBe($token2);
});

it('shows a published entry via preview token', function () {
    $entry = Entry::factory()->published()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'title' => 'Publikovaný náhled',
        'slug' => 'publikovany-nahled',
        'locale' => 'cs',
    ]);

    $token = $entry->generatePreviewToken();

    $response = $this->get("/_preview/{$token}");

    $response->assertSuccessful();
    $response->assertSee('Publikovaný náhled');
});

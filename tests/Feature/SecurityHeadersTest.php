<?php

use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;

beforeEach(function () {
    $collection = Collection::factory()->create([
        'handle' => 'pages',
        'route_template' => '/{slug}',
    ]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'slug' => 'test-page',
        'status' => EntryStatus::Published,
        'published_at' => now()->subDay(),
        'locale' => 'cs',
    ]);
});

it('adds X-Content-Type-Options header', function () {
    $this->get('/test-page')
        ->assertHeader('X-Content-Type-Options', 'nosniff');
});

it('adds X-Frame-Options header', function () {
    $this->get('/test-page')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN');
});

it('adds Referrer-Policy header', function () {
    $this->get('/test-page')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
});

it('adds Permissions-Policy header', function () {
    $this->get('/test-page')
        ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
});

it('adds X-XSS-Protection header', function () {
    $this->get('/test-page')
        ->assertHeader('X-XSS-Protection', '1; mode=block');
});

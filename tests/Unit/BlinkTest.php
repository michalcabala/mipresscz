<?php

use MiPressCz\Core\Support\Blink;

it('stores and retrieves values', function () {
    $blink = new Blink;
    $blink->put('key', 'value');

    expect($blink->get('key'))->toBe('value');
});

it('returns default when key does not exist', function () {
    $blink = new Blink;

    expect($blink->get('missing', 'fallback'))->toBe('fallback');
});

it('checks key existence', function () {
    $blink = new Blink;
    $blink->put('exists', true);

    expect($blink->has('exists'))->toBeTrue()
        ->and($blink->has('missing'))->toBeFalse();
});

it('forgets a key', function () {
    $blink = new Blink;
    $blink->put('key', 'value');
    $blink->forget('key');

    expect($blink->has('key'))->toBeFalse();
});

it('flushes everything', function () {
    $blink = new Blink;
    $blink->put('a', 1);
    $blink->put('b', 2);
    $blink->flush();

    expect($blink->has('a'))->toBeFalse()
        ->and($blink->has('b'))->toBeFalse();
});

it('once caches the callback result', function () {
    $blink = new Blink;
    $counter = 0;

    $first = $blink->once('key', function () use (&$counter) {
        $counter++;

        return 'computed';
    });

    $second = $blink->once('key', function () use (&$counter) {
        $counter++;

        return 'should not run';
    });

    expect($first)->toBe('computed')
        ->and($second)->toBe('computed')
        ->and($counter)->toBe(1);
});

it('once caches null values', function () {
    $blink = new Blink;
    $counter = 0;

    $blink->once('null-key', function () use (&$counter) {
        $counter++;

        return null;
    });

    $result = $blink->once('null-key', function () use (&$counter) {
        $counter++;

        return 'should not run';
    });

    expect($result)->toBeNull()
        ->and($counter)->toBe(1);
});

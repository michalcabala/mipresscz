<?php

use MiPressCz\Core\Support\RevisionDiffer;

it('generates diff html with del and ins tags for changed words', function () {
    $html = RevisionDiffer::diffWords('Hello world', 'Hello PHP');

    expect($html)
        ->toContain('<del')
        ->toContain('<ins')
        ->toContain('world')
        ->toContain('PHP');
});

it('returns plain escaped text when strings are identical', function () {
    $html = RevisionDiffer::diffWords('Same text', 'Same text');

    expect($html)
        ->toBe('Same text')
        ->not->toContain('<del')
        ->not->toContain('<ins');
});

it('marks inserted words in green ins tags', function () {
    $html = RevisionDiffer::diffWords('Hello', 'Hello world');

    expect($html)->toContain('<ins class="bg-green-100')
        ->toContain('world');
});

it('marks deleted words in red del tags', function () {
    $html = RevisionDiffer::diffWords('Hello world', 'Hello');

    expect($html)->toContain('<del class="bg-red-100')
        ->toContain('world');
});

it('escapes html special characters in diff output', function () {
    $html = RevisionDiffer::diffWords('<old>', '<new>');

    expect($html)->toContain('&lt;old&gt;')
        ->toContain('&lt;new&gt;')
        ->not->toContain('<old>')
        ->not->toContain('<new>');
});

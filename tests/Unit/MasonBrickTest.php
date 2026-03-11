<?php

use App\Mason\BrickCollection;
use App\Mason\Button;
use App\Mason\Columns;
use App\Mason\Divider;
use App\Mason\Gallery;
use App\Mason\Heading;
use App\Mason\Hero;
use App\Mason\Html;
use App\Mason\Image;
use App\Mason\Quote;
use App\Mason\Testimonial;
use App\Mason\Text;
use App\Mason\Video;

uses(Tests\TestCase::class);

// ── BrickCollection ────────────────────────────────────────────────────────

it('returns all 12 bricks from BrickCollection', function () {
    expect(BrickCollection::all())->toHaveCount(12);
});

it('BrickCollection contains expected brick classes', function () {
    $classes = BrickCollection::all();

    expect($classes)->toContain(Text::class)
        ->toContain(Heading::class)
        ->toContain(Hero::class)
        ->toContain(Button::class)
        ->toContain(Divider::class)
        ->toContain(Image::class)
        ->toContain(Gallery::class)
        ->toContain(Quote::class)
        ->toContain(Video::class)
        ->toContain(Columns::class)
        ->toContain(Html::class)
        ->toContain(Testimonial::class);
});

// ── getId ──────────────────────────────────────────────────────────────────

it('each brick returns the correct id', function (string $class, string $expectedId) {
    expect($class::getId())->toBe($expectedId);
})->with([
    [Text::class, 'text'],
    [Heading::class, 'heading'],
    [Hero::class, 'hero'],
    [Button::class, 'button'],
    [Divider::class, 'divider'],
    [Image::class, 'image'],
    [Gallery::class, 'gallery'],
    [Quote::class, 'quote'],
    [Video::class, 'video'],
    [Columns::class, 'columns'],
    [Html::class, 'html'],
    [Testimonial::class, 'testimonial'],
]);

// ── toHtml — content output ────────────────────────────────────────────────

it('Text brick renders provided content', function () {
    $html = Text::toHtml(['content' => '<p>Hello World</p>']);

    expect($html)
        ->toBeString()
        ->toContain('Hello World')
        ->toContain('mason-text');
});

it('Text brick returns null-like output for empty content', function () {
    $html = Text::toHtml([]);

    expect($html)->toBeString();
    expect($html)->not->toContain('mason-text prose');
});

it('Heading brick renders text and correct HTML level tag', function () {
    $html = Heading::toHtml(['text' => 'My Title', 'level' => 'h3']);

    expect($html)
        ->toBeString()
        ->toContain('My Title')
        ->toContain('<h3')
        ->toContain('mason-heading');
});

it('Heading brick defaults to h2 when level is omitted', function () {
    $html = Heading::toHtml(['text' => 'Default Level']);

    expect($html)->toContain('<h2');
});

it('Divider brick renders hr element', function () {
    $html = Divider::toHtml([]);

    expect($html)
        ->toBeString()
        ->toContain('<hr')
        ->toContain('mason-divider');
});

it('Hero brick renders heading and subheading', function () {
    $html = Hero::toHtml([
        'heading' => 'Welcome Hero',
        'subheading' => 'A great subheading',
        'alignment' => 'center',
    ]);

    expect($html)
        ->toBeString()
        ->toContain('Welcome Hero')
        ->toContain('A great subheading')
        ->toContain('mason-hero');
});

it('Hero brick renders button link when label and url are provided', function () {
    $html = Hero::toHtml([
        'heading' => 'Hero',
        'button_label' => 'Click me',
        'button_url' => 'https://example.com',
    ]);

    expect($html)
        ->toContain('Click me')
        ->toContain('https://example.com');
});

it('Button brick renders label and url', function () {
    $html = Button::toHtml([
        'label' => 'Get Started',
        'url' => 'https://example.com',
        'variant' => 'primary',
        'alignment' => 'center',
    ]);

    expect($html)
        ->toBeString()
        ->toContain('Get Started')
        ->toContain('https://example.com')
        ->toContain('mason-button');
});

it('Button brick renders nothing when label or url is missing', function () {
    $html = Button::toHtml(['label' => 'Missing URL']);

    expect($html)->not->toContain('mason-button');
});

it('Button brick opens in new tab when enabled', function () {
    $html = Button::toHtml([
        'label' => 'External',
        'url' => 'https://example.com',
        'open_in_new_tab' => true,
    ]);

    expect($html)->toContain('target="_blank"');
});

it('Quote brick renders quote text', function () {
    $html = Quote::toHtml(['text' => 'To be or not to be.', 'author' => 'Shakespeare']);

    expect($html)
        ->toBeString()
        ->toContain('To be or not to be.')
        ->toContain('Shakespeare')
        ->toContain('mason-quote');
});

it('Html brick renders raw HTML content', function () {
    $html = Html::toHtml(['code' => '<script>alert("test")</script>']);

    expect($html)->toBeString();
});

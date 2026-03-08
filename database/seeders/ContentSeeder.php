<?php

namespace Database\Seeders;

use App\Enums\DateBehavior;
use App\Enums\DefaultStatus;
use App\Enums\EntryStatus;
use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;
use App\Models\Taxonomy;
use App\Models\Term;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    /**
     * Wrap HTML content as a single Mason TextBrick array.
     *
     * @return array<int, array<string, mixed>>
     */
    private static function textBrick(string $html): array
    {
        return [
            [
                'type' => 'masonBrick',
                'attrs' => [
                    'id' => 'text',
                    'config' => ['content' => $html],
                ],
            ],
        ];
    }

    public function run(): void
    {
        $author = User::first() ?? User::factory()->create(['name' => 'Admin', 'email' => 'admin@mipress.cz']);

        // ── Collections & Blueprints ──

        $pages = Collection::updateOrCreate(['handle' => 'pages'], [
            'name' => 'pages',
            'title' => 'Stránky',
            'handle' => 'pages',
            'description' => 'Statické stránky webu',
            'is_tree' => true,
            'route_template' => '/{slug}',
            'sort_field' => 'order',
            'sort_direction' => 'asc',
            'date_behavior' => DateBehavior::None,
            'default_status' => DefaultStatus::Draft,
            'icon' => 'fal-file-lines',
            'is_active' => true,
        ]);

        $standardPage = Blueprint::updateOrCreate(
            ['collection_id' => $pages->id, 'handle' => 'standard'],
            [
                'name' => 'standard',
                'title' => 'Standardní stránka',
                'is_default' => true,
                'fields' => [
                    ['handle' => 'featured_image', 'type' => 'curator', 'display' => 'Hlavní obrázek', 'instructions' => '', 'required' => false, 'translatable' => false, 'width' => 100, 'section' => 'sidebar', 'config' => ['max_files' => 1, 'accepted_types' => ['image/*']], 'order' => 1, 'conditions' => []],
                ],
            ],
        );

        $landingPage = Blueprint::updateOrCreate(
            ['collection_id' => $pages->id, 'handle' => 'landing'],
            [
                'name' => 'landing',
                'title' => 'Landing page',
                'is_default' => false,
                'fields' => [
                    ['handle' => 'hero_heading', 'type' => 'text', 'display' => 'Hero nadpis', 'instructions' => '', 'required' => true, 'translatable' => true, 'width' => 100, 'section' => 'main', 'config' => [], 'order' => 1, 'conditions' => []],
                    ['handle' => 'hero_image', 'type' => 'curator', 'display' => 'Hero obrázek', 'instructions' => '', 'required' => false, 'translatable' => false, 'width' => 100, 'section' => 'main', 'config' => ['max_files' => 1], 'order' => 2, 'conditions' => []],
                ],
            ],
        );

        $articles = Collection::updateOrCreate(['handle' => 'articles'], [
            'name' => 'articles',
            'title' => 'Články',
            'handle' => 'articles',
            'description' => 'Blogové články',
            'is_tree' => false,
            'route_template' => '/blog/{slug}',
            'sort_field' => 'published_at',
            'sort_direction' => 'desc',
            'date_behavior' => DateBehavior::Required,
            'revisions_enabled' => true,
            'default_status' => DefaultStatus::Draft,
            'icon' => 'fal-newspaper',
            'is_active' => true,
        ]);

        $articleBlueprint = Blueprint::updateOrCreate(
            ['collection_id' => $articles->id, 'handle' => 'article'],
            [
                'name' => 'article',
                'title' => 'Článek',
                'is_default' => true,
                'fields' => [
                    ['handle' => 'featured_image', 'type' => 'curator', 'display' => 'Hlavní obrázek', 'instructions' => '', 'required' => false, 'translatable' => false, 'width' => 100, 'section' => 'sidebar', 'config' => ['max_files' => 1, 'accepted_types' => ['image/*']], 'order' => 1, 'conditions' => []],
                    ['handle' => 'excerpt', 'type' => 'textarea', 'display' => 'Perex', 'instructions' => 'Krátký výtah článku', 'required' => false, 'translatable' => true, 'width' => 100, 'section' => 'sidebar', 'config' => [], 'order' => 2, 'conditions' => []],
                    ['handle' => 'related_articles', 'type' => 'entries', 'display' => 'Související články', 'instructions' => '', 'required' => false, 'translatable' => false, 'width' => 100, 'section' => 'sidebar', 'config' => ['collections' => ['articles'], 'max_items' => 3, 'create' => false], 'order' => 3, 'conditions' => []],
                ],
            ],
        );

        $testimonials = Collection::updateOrCreate(['handle' => 'testimonials'], [
            'name' => 'testimonials',
            'title' => 'Reference',
            'handle' => 'testimonials',
            'description' => 'Reference a doporučení',
            'is_tree' => false,
            'route_template' => null,
            'date_behavior' => DateBehavior::None,
            'default_status' => DefaultStatus::Published,
            'icon' => 'fal-quote-right',
            'is_active' => true,
        ]);

        $testimonialBlueprint = Blueprint::updateOrCreate(
            ['collection_id' => $testimonials->id, 'handle' => 'testimonial'],
            [
                'name' => 'testimonial',
                'title' => 'Reference',
                'is_default' => true,
                'fields' => [
                    ['handle' => 'author_name', 'type' => 'text', 'display' => 'Jméno autora', 'instructions' => '', 'required' => true, 'translatable' => false, 'width' => 50, 'section' => 'main', 'config' => [], 'order' => 1, 'conditions' => []],
                    ['handle' => 'company', 'type' => 'text', 'display' => 'Společnost', 'instructions' => '', 'required' => false, 'translatable' => false, 'width' => 50, 'section' => 'main', 'config' => [], 'order' => 2, 'conditions' => []],
                    ['handle' => 'quote', 'type' => 'textarea', 'display' => 'Text reference', 'instructions' => '', 'required' => true, 'translatable' => true, 'width' => 100, 'section' => 'main', 'config' => [], 'order' => 3, 'conditions' => []],
                    ['handle' => 'rating', 'type' => 'number', 'display' => 'Hodnocení', 'instructions' => '1-5', 'required' => false, 'translatable' => false, 'width' => 50, 'section' => 'sidebar', 'config' => ['min' => 1, 'max' => 5], 'order' => 4, 'conditions' => []],
                ],
            ],
        );

        // ── Taxonomies & Terms ──

        $categories = Taxonomy::updateOrCreate(['handle' => 'categories'], [
            'name' => 'categories',
            'title' => 'Kategorie',
            'handle' => 'categories',
            'is_hierarchical' => true,
            'is_active' => true,
        ]);

        $tags = Taxonomy::updateOrCreate(['handle' => 'tags'], [
            'name' => 'tags',
            'title' => 'Štítky',
            'handle' => 'tags',
            'is_hierarchical' => false,
            'is_active' => true,
        ]);

        // Link taxonomies to collections
        $articles->taxonomies()->syncWithoutDetaching([$categories->id, $tags->id]);
        $pages->taxonomies()->syncWithoutDetaching([$categories->id]);

        // Category terms (hierarchical)
        $tech = Term::updateOrCreate(['taxonomy_id' => $categories->id, 'slug' => 'technologie'], [
            'title' => 'Technologie', 'slug' => 'technologie', 'order' => 1,
        ]);
        $php = Term::updateOrCreate(['taxonomy_id' => $categories->id, 'slug' => 'php'], [
            'title' => 'PHP', 'slug' => 'php', 'parent_id' => $tech->id, 'order' => 1,
        ]);
        $laravel = Term::updateOrCreate(['taxonomy_id' => $categories->id, 'slug' => 'laravel'], [
            'title' => 'Laravel', 'slug' => 'laravel', 'parent_id' => $tech->id, 'order' => 2,
        ]);
        $js = Term::updateOrCreate(['taxonomy_id' => $categories->id, 'slug' => 'javascript'], [
            'title' => 'JavaScript', 'slug' => 'javascript', 'parent_id' => $tech->id, 'order' => 3,
        ]);
        $design = Term::updateOrCreate(['taxonomy_id' => $categories->id, 'slug' => 'design'], [
            'title' => 'Design', 'slug' => 'design', 'order' => 2,
        ]);
        $ui = Term::updateOrCreate(['taxonomy_id' => $categories->id, 'slug' => 'ui'], [
            'title' => 'UI', 'slug' => 'ui', 'parent_id' => $design->id, 'order' => 1,
        ]);
        $ux = Term::updateOrCreate(['taxonomy_id' => $categories->id, 'slug' => 'ux'], [
            'title' => 'UX', 'slug' => 'ux', 'parent_id' => $design->id, 'order' => 2,
        ]);

        // Tag terms (flat)
        $tagTutorial = Term::updateOrCreate(['taxonomy_id' => $tags->id, 'slug' => 'tutorial'], [
            'title' => 'Tutorial', 'slug' => 'tutorial', 'order' => 1,
        ]);
        $tagNovinka = Term::updateOrCreate(['taxonomy_id' => $tags->id, 'slug' => 'novinka'], [
            'title' => 'Novinka', 'slug' => 'novinka', 'order' => 2,
        ]);
        $tagTip = Term::updateOrCreate(['taxonomy_id' => $tags->id, 'slug' => 'tip'], [
            'title' => 'Tip', 'slug' => 'tip', 'order' => 3,
        ]);
        $tagRecenze = Term::updateOrCreate(['taxonomy_id' => $tags->id, 'slug' => 'recenze'], [
            'title' => 'Recenze', 'slug' => 'recenze', 'order' => 4,
        ]);

        // ── Pages (tree structure) ──

        $homepage = Entry::updateOrCreate(
            ['collection_id' => $pages->id, 'slug' => 'uvod', 'locale' => 'cs'],
            [
                'blueprint_id' => $standardPage->id,
                'title' => 'Úvod',
                'slug' => 'uvod',
                'status' => EntryStatus::Published,
                'published_at' => now(),
                'author_id' => $author->id,
                'order' => 1,
                'data' => [],
                'content' => self::textBrick('<h1>Vítejte na miPress</h1><p>Moderní CMS postavený na Laravel a Filament. Jednoduchý, rychlý a flexibilní.</p>'),
            ],
        );

        $about = Entry::updateOrCreate(
            ['collection_id' => $pages->id, 'slug' => 'o-nas', 'locale' => 'cs'],
            [
                'blueprint_id' => $standardPage->id,
                'title' => 'O nás',
                'slug' => 'o-nas',
                'status' => EntryStatus::Published,
                'published_at' => now(),
                'author_id' => $author->id,
                'parent_id' => $homepage->id,
                'order' => 2,
                'data' => [],
                'content' => self::textBrick('<h1>O nás</h1><p>Jsme tým vývojářů, kteří milují open-source a Laravel ekosystém.</p>'),
            ],
        );

        $contact = Entry::updateOrCreate(
            ['collection_id' => $pages->id, 'slug' => 'kontakt', 'locale' => 'cs'],
            [
                'blueprint_id' => $standardPage->id,
                'title' => 'Kontakt',
                'slug' => 'kontakt',
                'status' => EntryStatus::Published,
                'published_at' => now(),
                'author_id' => $author->id,
                'parent_id' => $homepage->id,
                'order' => 3,
                'data' => [],
                'content' => self::textBrick('<h1>Kontakt</h1><p>Napište nám na <a href="mailto:info@mipress.cz">info@mipress.cz</a>.</p>'),
            ],
        );

        // ── Articles ──

        $article1 = Entry::updateOrCreate(
            ['collection_id' => $articles->id, 'slug' => 'uvod-do-laravel', 'locale' => 'cs'],
            [
                'blueprint_id' => $articleBlueprint->id,
                'title' => 'Úvod do Laravelu',
                'slug' => 'uvod-do-laravel',
                'status' => EntryStatus::Published,
                'published_at' => now()->subDays(10),
                'author_id' => $author->id,
                'order' => 1,
                'data' => ['excerpt' => 'Seznamte se s Laravel frameworkem — nejpopulárnějším PHP frameworkem současnosti.'],
                'content' => self::textBrick('<p>Laravel je moderní PHP framework pro webové aplikace. V tomto článku si ukážeme základy.</p>'),
            ],
        );
        $article1->terms()->syncWithoutDetaching([$laravel->id, $php->id, $tagTutorial->id]);

        $article2 = Entry::updateOrCreate(
            ['collection_id' => $articles->id, 'slug' => 'filament-admin-panel', 'locale' => 'cs'],
            [
                'blueprint_id' => $articleBlueprint->id,
                'title' => 'Filament — admin panel pro Laravel',
                'slug' => 'filament-admin-panel',
                'status' => EntryStatus::Published,
                'published_at' => now()->subDays(7),
                'author_id' => $author->id,
                'order' => 2,
                'data' => ['excerpt' => 'Filament přináší elegantní řešení pro stavbu admin panelů.'],
                'content' => self::textBrick('<p>Filament je nejrychlejší způsob, jak vytvořit administrační rozhraní v Laravelu.</p>'),
            ],
        );
        $article2->terms()->syncWithoutDetaching([$laravel->id, $php->id, $tagNovinka->id]);

        $article3 = Entry::updateOrCreate(
            ['collection_id' => $articles->id, 'slug' => 'tailwind-css-tipy', 'locale' => 'cs'],
            [
                'blueprint_id' => $articleBlueprint->id,
                'title' => '10 tipů pro Tailwind CSS',
                'slug' => 'tailwind-css-tipy',
                'status' => EntryStatus::Published,
                'published_at' => now()->subDays(4),
                'author_id' => $author->id,
                'order' => 3,
                'data' => ['excerpt' => 'Vylepšete svůj workflow s Tailwind CSS pomocí těchto tipů.'],
                'content' => self::textBrick('<p>Tailwind CSS je utility-first CSS framework. Zde je 10 tipů pro efektivní práci.</p>'),
            ],
        );
        $article3->terms()->syncWithoutDetaching([$ui->id, $js->id, $tagTip->id]);

        $article4 = Entry::updateOrCreate(
            ['collection_id' => $articles->id, 'slug' => 'livewire-interaktivni-komponenty', 'locale' => 'cs'],
            [
                'blueprint_id' => $articleBlueprint->id,
                'title' => 'Livewire: Interaktivní komponenty bez JavaScriptu',
                'slug' => 'livewire-interaktivni-komponenty',
                'status' => EntryStatus::Published,
                'published_at' => now()->subDays(2),
                'author_id' => $author->id,
                'order' => 4,
                'data' => ['excerpt' => 'Objevte sílu Livewire pro tvorbu dynamických komponent.'],
                'content' => self::textBrick('<p>Livewire umožňuje stavět interaktivní rozhraní přímo v PHP. Žádný JavaScript není potřeba.</p>'),
            ],
        );
        $article4->terms()->syncWithoutDetaching([$laravel->id, $tagTutorial->id]);

        $article5 = Entry::updateOrCreate(
            ['collection_id' => $articles->id, 'slug' => 'pest-testovani-v-laravel', 'locale' => 'cs'],
            [
                'blueprint_id' => $articleBlueprint->id,
                'title' => 'Testování v Laravelu s Pest PHP',
                'slug' => 'pest-testovani-v-laravel',
                'status' => EntryStatus::Published,
                'published_at' => now()->subDays(1),
                'author_id' => $author->id,
                'order' => 5,
                'data' => ['excerpt' => 'Naučte se psát testy s Pest PHP — moderní alternativou k PHPUnit.'],
                'content' => self::textBrick('<p>Pest PHP je elegantní testovací framework. Ukážeme si, jak ho využít v Laravel projektu.</p>'),
            ],
        );
        $article5->terms()->syncWithoutDetaching([$php->id, $laravel->id, $tagTutorial->id, $tagRecenze->id]);

        // English translation of article 1
        Entry::updateOrCreate(
            ['collection_id' => $articles->id, 'slug' => 'introduction-to-laravel', 'locale' => 'en'],
            [
                'blueprint_id' => $articleBlueprint->id,
                'origin_id' => $article1->id,
                'title' => 'Introduction to Laravel',
                'slug' => 'introduction-to-laravel',
                'locale' => 'en',
                'status' => EntryStatus::Published,
                'published_at' => now()->subDays(10),
                'author_id' => $author->id,
                'order' => 1,
                'data' => ['excerpt' => 'Get to know the Laravel framework — the most popular PHP framework today.'],
                'content' => self::textBrick('<p>Laravel is a modern PHP framework for web applications. In this article we will show you the basics.</p>'),
            ],
        );

        // ── Testimonials ──

        Entry::updateOrCreate(
            ['collection_id' => $testimonials->id, 'slug' => 'jan-novak', 'locale' => 'cs'],
            [
                'blueprint_id' => $testimonialBlueprint->id,
                'title' => 'Jan Novák — reference',
                'slug' => 'jan-novak',
                'status' => EntryStatus::Published,
                'published_at' => now(),
                'author_id' => $author->id,
                'order' => 1,
                'data' => [
                    'author_name' => 'Jan Novák',
                    'company' => 'Novák IT s.r.o.',
                    'quote' => 'miPress je přesně to, co jsme hledali. Jednoduchý na správu a výkonný pod kapotou.',
                    'rating' => 5,
                ],
            ],
        );

        Entry::updateOrCreate(
            ['collection_id' => $testimonials->id, 'slug' => 'petra-svobodova', 'locale' => 'cs'],
            [
                'blueprint_id' => $testimonialBlueprint->id,
                'title' => 'Petra Svobodová — reference',
                'slug' => 'petra-svobodova',
                'status' => EntryStatus::Published,
                'published_at' => now(),
                'author_id' => $author->id,
                'order' => 2,
                'data' => [
                    'author_name' => 'Petra Svobodová',
                    'company' => 'Design Studio Praha',
                    'quote' => 'Konečně CMS, které nebolí. Administrace je intuitivní a web je rychlý.',
                    'rating' => 4,
                ],
            ],
        );

        Entry::updateOrCreate(
            ['collection_id' => $testimonials->id, 'slug' => 'martin-dvorak', 'locale' => 'cs'],
            [
                'blueprint_id' => $testimonialBlueprint->id,
                'title' => 'Martin Dvořák — reference',
                'slug' => 'martin-dvorak',
                'status' => EntryStatus::Published,
                'published_at' => now(),
                'author_id' => $author->id,
                'order' => 3,
                'data' => [
                    'author_name' => 'Martin Dvořák',
                    'company' => 'Dvořák Consulting',
                    'quote' => 'Přesně takový CMS jsem chtěl pro naše klienty. Flexibilní, bezpečný a moderní.',
                    'rating' => 5,
                ],
            ],
        );
    }
}

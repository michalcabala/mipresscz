<?php

namespace Database\Seeders;

use App\Models\Block;
use Illuminate\Database\Seeder;

class BlocksSeeder extends Seeder
{
    public function run(): void
    {
        $blocks = [
            [
                'name' => 'hero',
                'title' => 'Hero sekce',
                'handle' => 'hero',
                'icon' => 'fal-panorama',
                'category' => 'Layout',
                'description' => 'Hlavní hero sekce s nadpisem, podnadpisem a pozadím',
                'order' => 1,
                'fields' => [
                    ['handle' => 'heading', 'type' => 'text', 'display' => 'Nadpis', 'required' => true, 'translatable' => true, 'width' => 100, 'section' => 'main', 'config' => [], 'order' => 1],
                    ['handle' => 'subheading', 'type' => 'text', 'display' => 'Podnadpis', 'required' => false, 'translatable' => true, 'width' => 100, 'section' => 'main', 'config' => [], 'order' => 2],
                    ['handle' => 'background_image', 'type' => 'media', 'display' => 'Obrázek pozadí', 'required' => false, 'translatable' => false, 'width' => 100, 'section' => 'main', 'config' => ['max_files' => 1, 'accepted_types' => ['image/*']], 'order' => 3],
                    ['handle' => 'cta_text', 'type' => 'text', 'display' => 'Text tlačítka', 'required' => false, 'translatable' => true, 'width' => 50, 'section' => 'main', 'config' => [], 'order' => 4],
                    ['handle' => 'cta_url', 'type' => 'url', 'display' => 'URL tlačítka', 'required' => false, 'translatable' => false, 'width' => 50, 'section' => 'main', 'config' => [], 'order' => 5],
                ],
            ],
            [
                'name' => 'text',
                'title' => 'Text',
                'handle' => 'text',
                'icon' => 'fal-align-left',
                'category' => 'Obsah',
                'description' => 'Textový blok s rich editorem',
                'order' => 2,
                'fields' => [
                    ['handle' => 'content', 'type' => 'rich_editor', 'display' => 'Obsah', 'required' => true, 'translatable' => true, 'width' => 100, 'section' => 'main', 'config' => [], 'order' => 1],
                ],
            ],
            [
                'name' => 'text_image',
                'title' => 'Text s obrázkem',
                'handle' => 'text_image',
                'icon' => 'fal-newspaper',
                'category' => 'Obsah',
                'description' => 'Text s obrázkem vedle sebe',
                'order' => 3,
                'fields' => [
                    ['handle' => 'text', 'type' => 'rich_editor', 'display' => 'Text', 'required' => true, 'translatable' => true, 'width' => 100, 'section' => 'main', 'config' => [], 'order' => 1],
                    ['handle' => 'image', 'type' => 'media', 'display' => 'Obrázek', 'required' => true, 'translatable' => false, 'width' => 100, 'section' => 'main', 'config' => ['max_files' => 1], 'order' => 2],
                    ['handle' => 'image_position', 'type' => 'select', 'display' => 'Pozice obrázku', 'required' => true, 'translatable' => false, 'width' => 50, 'section' => 'main', 'config' => ['options' => ['left' => 'Vlevo', 'right' => 'Vpravo']], 'order' => 3],
                ],
            ],
            [
                'name' => 'gallery',
                'title' => 'Galerie',
                'handle' => 'gallery',
                'icon' => 'fal-images',
                'category' => 'Média',
                'description' => 'Galerie obrázků',
                'order' => 4,
                'fields' => [
                    ['handle' => 'images', 'type' => 'media', 'display' => 'Obrázky', 'required' => true, 'translatable' => false, 'width' => 100, 'section' => 'main', 'config' => ['max_files' => 20, 'accepted_types' => ['image/*']], 'order' => 1],
                    ['handle' => 'columns', 'type' => 'select', 'display' => 'Počet sloupců', 'required' => true, 'translatable' => false, 'width' => 50, 'section' => 'main', 'config' => ['options' => ['2' => '2', '3' => '3', '4' => '4']], 'order' => 2],
                ],
            ],
            [
                'name' => 'video',
                'title' => 'Video',
                'handle' => 'video',
                'icon' => 'fal-video',
                'category' => 'Média',
                'description' => 'Vložené video',
                'order' => 5,
                'fields' => [
                    ['handle' => 'url', 'type' => 'url', 'display' => 'URL videa', 'required' => true, 'translatable' => false, 'width' => 100, 'section' => 'main', 'config' => [], 'order' => 1],
                    ['handle' => 'poster', 'type' => 'media', 'display' => 'Náhledový obrázek', 'required' => false, 'translatable' => false, 'width' => 100, 'section' => 'main', 'config' => ['max_files' => 1], 'order' => 2],
                ],
            ],
            [
                'name' => 'quote',
                'title' => 'Citát',
                'handle' => 'quote',
                'icon' => 'fal-quote-right',
                'category' => 'Obsah',
                'description' => 'Citát s autorem',
                'order' => 6,
                'fields' => [
                    ['handle' => 'text', 'type' => 'textarea', 'display' => 'Text citátu', 'required' => true, 'translatable' => true, 'width' => 100, 'section' => 'main', 'config' => [], 'order' => 1],
                    ['handle' => 'author', 'type' => 'text', 'display' => 'Autor', 'required' => true, 'translatable' => false, 'width' => 50, 'section' => 'main', 'config' => [], 'order' => 2],
                    ['handle' => 'source', 'type' => 'text', 'display' => 'Zdroj', 'required' => false, 'translatable' => false, 'width' => 50, 'section' => 'main', 'config' => [], 'order' => 3],
                ],
            ],
            [
                'name' => 'cta',
                'title' => 'Výzva k akci',
                'handle' => 'cta',
                'icon' => 'fal-bullhorn',
                'category' => 'Layout',
                'description' => 'Call to action blok',
                'order' => 7,
                'fields' => [
                    ['handle' => 'heading', 'type' => 'text', 'display' => 'Nadpis', 'required' => true, 'translatable' => true, 'width' => 100, 'section' => 'main', 'config' => [], 'order' => 1],
                    ['handle' => 'text', 'type' => 'textarea', 'display' => 'Text', 'required' => false, 'translatable' => true, 'width' => 100, 'section' => 'main', 'config' => [], 'order' => 2],
                    ['handle' => 'button_text', 'type' => 'text', 'display' => 'Text tlačítka', 'required' => true, 'translatable' => true, 'width' => 50, 'section' => 'main', 'config' => [], 'order' => 3],
                    ['handle' => 'button_url', 'type' => 'url', 'display' => 'URL tlačítka', 'required' => true, 'translatable' => false, 'width' => 50, 'section' => 'main', 'config' => [], 'order' => 4],
                    ['handle' => 'style', 'type' => 'select', 'display' => 'Styl', 'required' => true, 'translatable' => false, 'width' => 50, 'section' => 'main', 'config' => ['options' => ['primary' => 'Primární', 'secondary' => 'Sekundární']], 'order' => 5],
                ],
            ],
            [
                'name' => 'accordion',
                'title' => 'Akordeon',
                'handle' => 'accordion',
                'icon' => 'fal-bars-staggered',
                'category' => 'Obsah',
                'description' => 'Rozbalovací sekce (FAQ apod.)',
                'order' => 8,
                'fields' => [
                    ['handle' => 'items', 'type' => 'json', 'display' => 'Položky', 'required' => true, 'translatable' => true, 'width' => 100, 'section' => 'main', 'config' => ['schema' => ['title' => 'text', 'content' => 'rich_editor']], 'order' => 1],
                ],
            ],
            [
                'name' => 'cards',
                'title' => 'Karty',
                'handle' => 'cards',
                'icon' => 'fal-table-cells',
                'category' => 'Layout',
                'description' => 'Mřížka karet s titulkem, textem a obrázkem',
                'order' => 9,
                'fields' => [
                    ['handle' => 'cards', 'type' => 'json', 'display' => 'Karty', 'required' => true, 'translatable' => true, 'width' => 100, 'section' => 'main', 'config' => ['schema' => ['title' => 'text', 'text' => 'textarea', 'image' => 'media', 'url' => 'url']], 'order' => 1],
                ],
            ],
        ];

        foreach ($blocks as $block) {
            Block::updateOrCreate(
                ['handle' => $block['handle']],
                $block,
            );
        }
    }
}

<?php

namespace MiPressCz\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use MiPressCz\Core\Enums\DateBehavior;
use MiPressCz\Core\Enums\DefaultStatus;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;

class DefaultCollectionsSeeder extends Seeder
{
    public function run(): void
    {
        $pages = Collection::updateOrCreate(
            ['handle' => 'pages'],
            [
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
            ],
        );

        Blueprint::updateOrCreate(
            ['collection_id' => $pages->id, 'handle' => 'standard'],
            [
                'name' => 'standard',
                'title' => 'Standardní stránka',
                'is_default' => true,
                'fields' => [],
            ],
        );
    }
}

<?php

namespace MiPressCz\Core\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MiPressCz\Core\Enums\DateBehavior;
use MiPressCz\Core\Enums\DefaultStatus;
use MiPressCz\Core\Models\Collection;

/**
 * @extends Factory<Collection>
 */
class CollectionFactory extends Factory
{
    protected $model = Collection::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'name' => $name,
            'title' => ucfirst($name),
            'handle' => $name,
            'description' => fake()->sentence(),
            'is_tree' => false,
            'route_template' => '/'.$name.'/{slug}',
            'sort_field' => 'order',
            'sort_direction' => 'asc',
            'date_behavior' => DateBehavior::None,
            'default_status' => DefaultStatus::Draft,
            'is_active' => true,
        ];
    }
}

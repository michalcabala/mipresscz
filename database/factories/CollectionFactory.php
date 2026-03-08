<?php

namespace Database\Factories;

use App\Enums\DateBehavior;
use App\Enums\DefaultStatus;
use App\Models\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'revisions_enabled' => false,
            'default_status' => DefaultStatus::Draft,
            'is_active' => true,
        ];
    }
}

<?php

namespace MiPressCz\Core\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;

/**
 * @extends Factory<Blueprint>
 */
class BlueprintFactory extends Factory
{
    protected $model = Blueprint::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'collection_id' => Collection::factory(),
            'name' => fake()->unique()->word(),
            'title' => fake()->words(2, true),
            'handle' => fake()->unique()->slug(2),
            'fields' => [],
            'is_default' => false,
            'use_mason' => false,
            'is_active' => true,
            'order' => 0,
        ];
    }

    public function default(): static
    {
        return $this->state(['is_default' => true]);
    }
}

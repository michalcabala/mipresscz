<?php

namespace Database\Factories;

use App\Models\Blueprint;
use App\Models\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'is_active' => true,
            'order' => 0,
        ];
    }

    public function default(): static
    {
        return $this->state(['is_default' => true]);
    }
}

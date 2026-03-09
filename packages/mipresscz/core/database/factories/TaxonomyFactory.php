<?php

namespace MiPressCz\Core\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MiPressCz\Core\Models\Taxonomy;

/**
 * @extends Factory<Taxonomy>
 */
class TaxonomyFactory extends Factory
{
    protected $model = Taxonomy::class;

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
            'is_hierarchical' => false,
            'is_active' => true,
        ];
    }

    public function hierarchical(): static
    {
        return $this->state(['is_hierarchical' => true]);
    }
}

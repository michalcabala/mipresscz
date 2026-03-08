<?php

namespace Database\Factories;

use App\Models\Block;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Block>
 */
class BlockFactory extends Factory
{
    protected $model = Block::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->words(2, true);

        return [
            'name' => Str::slug($title),
            'title' => $title,
            'handle' => Str::slug($title),
            'fields' => [],
            'icon' => 'fal-cube',
            'description' => fake()->sentence(),
            'is_active' => true,
            'order' => fake()->numberBetween(0, 100),
        ];
    }
}

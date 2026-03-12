<?php

namespace MiPressCz\Core\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MiPressCz\Core\Models\Menu;

/** @extends Factory<Menu> */
class MenuFactory extends Factory
{
    protected $model = Menu::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $handle = fake()->unique()->slug(2);

        return [
            'title' => fake()->words(2, true),
            'handle' => $handle,
            'location' => null,
            'description' => null,
        ];
    }

    public function primary(): static
    {
        return $this->state([
            'handle' => 'primary',
            'title' => 'Primary',
            'location' => 'primary',
        ]);
    }

    public function footer(): static
    {
        return $this->state([
            'handle' => 'footer',
            'title' => 'Footer',
            'location' => 'footer',
        ]);
    }
}

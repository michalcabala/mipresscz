<?php

namespace MiPressCz\Core\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MiPressCz\Core\Enums\MenuItemTarget;
use MiPressCz\Core\Enums\MenuItemType;
use MiPressCz\Core\Models\Menu;
use MiPressCz\Core\Models\MenuItem;

/** @extends Factory<MenuItem> */
class MenuItemFactory extends Factory
{
    protected $model = MenuItem::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'menu_id' => Menu::factory(),
            'parent_id' => null,
            'order' => 0,
            'type' => MenuItemType::CustomLink,
            'title' => fake()->words(2, true),
            'url' => fake()->url(),
            'target' => MenuItemTarget::Self,
            'entry_id' => null,
            'is_active' => true,
        ];
    }

    public function forEntry(): static
    {
        return $this->state([
            'type' => MenuItemType::Entry,
            'url' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}

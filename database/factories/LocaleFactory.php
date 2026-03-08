<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Locale>
 */
class LocaleFactory extends Factory
{
    public function definition(): array
    {
        $code = $this->faker->unique()->languageCode();

        return [
            'code' => $code,
            'name' => $this->faker->word(),
            'native_name' => $this->faker->word(),
            'flag' => null,
            'is_default' => false,
            'is_active' => true,
            'is_admin_available' => true,
            'is_frontend_available' => true,
            'direction' => 'ltr',
            'date_format' => 'd.m.Y',
            'url_prefix' => $code,
            'fallback_locale' => null,
            'order' => $this->faker->numberBetween(1, 100),
        ];
    }

    public function default(): static
    {
        return $this->state(['is_default' => true]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}

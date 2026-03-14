<?php

namespace MiPressCz\Core\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use MiPressCz\Core\Models\MediaTag;

/**
 * @extends Factory<MediaTag>
 */
class MediaTagFactory extends Factory
{
    protected $model = MediaTag::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}

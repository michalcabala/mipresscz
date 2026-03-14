<?php

namespace MiPressCz\Core\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use MiPressCz\Core\Models\MediaFolder;

/**
 * @extends Factory<MediaFolder>
 */
class MediaFolderFactory extends Factory
{
    protected $model = MediaFolder::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'parent_id' => null,
            'order' => 0,
        ];
    }
}

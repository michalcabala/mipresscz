<?php

namespace Database\Factories;

use App\Models\GlobalSet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<GlobalSet>
 */
class GlobalSetFactory extends Factory
{
    protected $model = GlobalSet::class;

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
            'data' => [],
            'locale' => 'cs',
        ];
    }
}

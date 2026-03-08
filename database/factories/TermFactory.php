<?php

namespace Database\Factories;

use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Term>
 */
class TermFactory extends Factory
{
    protected $model = Term::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->word();

        return [
            'taxonomy_id' => Taxonomy::factory(),
            'title' => ucfirst($title),
            'slug' => Str::slug($title),
            'order' => 0,
            'is_active' => true,
        ];
    }
}

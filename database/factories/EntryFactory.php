<?php

namespace Database\Factories;

use App\Enums\EntryStatus;
use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Entry>
 */
class EntryFactory extends Factory
{
    protected $model = Entry::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'collection_id' => Collection::factory(),
            'blueprint_id' => Blueprint::factory(),
            'locale' => 'cs',
            'title' => $title,
            'slug' => Str::slug($title),
            'data' => [],
            'status' => EntryStatus::Draft,
            'order' => 0,
            'author_id' => User::factory(),
            'is_pinned' => false,
        ];
    }

    public function published(): static
    {
        return $this->state([
            'status' => EntryStatus::Published,
            'published_at' => now(),
        ]);
    }

    public function scheduled(): static
    {
        return $this->state([
            'status' => EntryStatus::Scheduled,
            'published_at' => now()->addDays(7),
        ]);
    }
}

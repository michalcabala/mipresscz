<?php

namespace MiPressCz\Core\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\User;

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
            'content' => null,
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

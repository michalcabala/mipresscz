<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use MiPressCz\Core\Database\Seeders\ContentSeeder;
use MiPressCz\Core\Database\Seeders\GlobalsSeeder;
use MiPressCz\Core\Database\Seeders\LocaleSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            LocaleSeeder::class,
            GlobalsSeeder::class,
            ContentSeeder::class,
        ]);

        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}

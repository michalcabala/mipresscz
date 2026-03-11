<?php

namespace MiPressCz\Core\Console\Commands;

use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use MiPressCz\Core\Database\Seeders\ContentSeeder;
use MiPressCz\Core\Database\Seeders\GlobalsSeeder;
use MiPressCz\Core\Database\Seeders\LocaleSeeder;
use MiPressCz\Core\Models\Locale;

class InstallCommand extends Command
{
    protected $signature = 'mipresscz:install
        {--admin-name= : Full name of the admin user}
        {--admin-email= : Email address of the admin user}
        {--admin-password= : Password for the admin user}
        {--force : Re-run even if the application appears to be installed}
        {--seed : Seed demo content after installation}';

    protected $description = 'Install the miPressCZ CMS (migrations, roles, locale, admin user)';

    public function handle(): int
    {
        $this->components->info('Installing miPressCZ CMS...');

        if (! $this->option('force') && $this->isAlreadyInstalled()) {
            $this->components->warn('miPressCZ appears to be already installed. Use --force to re-run.');

            return self::FAILURE;
        }

        $this->runMigrations();
        $this->seedRolesAndPermissions();
        $this->seedLocales();
        $this->createAdminUser();

        if ($this->option('seed')) {
            $this->seedDemoContent();
        }

        $this->components->success('miPressCZ has been installed successfully.');

        return self::SUCCESS;
    }

    private function isAlreadyInstalled(): bool
    {
        try {
            return Locale::query()->exists();
        } catch (\Throwable) {
            return false;
        }
    }

    private function runMigrations(): void
    {
        $this->components->task('Running migrations', function () {
            Artisan::call('migrate', ['--force' => true, '--no-interaction' => true]);
        });
    }

    private function seedRolesAndPermissions(): void
    {
        $this->components->task('Seeding roles & permissions', function () {
            app(RolesAndPermissionsSeeder::class)->run();
        });
    }

    private function seedLocales(): void
    {
        $this->components->task('Seeding default locales', function () {
            app(LocaleSeeder::class)->run();
        });
    }

    private function createAdminUser(): void
    {
        $name = $this->option('admin-name')
            ?? $this->ask('Admin name', 'Admin');

        $email = $this->option('admin-email')
            ?? $this->ask('Admin email', 'admin@example.com');

        $password = $this->option('admin-password')
            ?? $this->secret('Admin password');

        /** @var class-string<\Illuminate\Database\Eloquent\Model> $userModel */
        $userModel = config('auth.providers.users.model', \App\Models\User::class);

        $this->components->task('Creating admin user', function () use ($userModel, $name, $email, $password) {
            $userModel::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make((string) $password),
                ],
            );
        });
    }

    private function seedDemoContent(): void
    {
        $this->components->task('Seeding globals', function () {
            app(GlobalsSeeder::class)->run();
        });

        $this->components->task('Seeding demo content', function () {
            app(ContentSeeder::class)->run();
        });
    }
}

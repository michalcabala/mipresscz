# miPress CMS — Copilot Instructions

## Project Overview
miPress is a modular CMS built on Laravel 12 + Filament 5, designed as a professional WordPress alternative for the Czech market. It targets company presentations, small business websites, and community projects (fan clubs, restaurants, portfolios).

## Stack
- PHP 8.3.4, Laravel 12, Filament 5, Livewire 4, Tailwind CSS 4
- Pest 4 for testing
- Spatie Laravel Permission v7 (no Filament Shield)
- Served by Laravel Herd at `mipresscz.test`

## Installed Packages
- spatie/laravel-permission — roles and permissions
- bezhansalleh/filament-language-switch — language switching (cs/en)
- jeffgreco13/filament-breezy — user profile, 2FA
- caresome/filament-auth-designer — auth page design
- spatie/laravel-medialibrary — media management

## Project Structure
- Filament panel: `admin` at `/mpcp` (AdminPanelProvider)
- Resources auto-discovered from `app/Filament/Resources/`
- Custom FontAwesome icon sets via blade-icons:
  - `fal-*` — FA Light (`resources/svg/fa/light/`)
  - `fab-*` — FA Brands (`resources/svg/fa/brands/`)
  - Prefix must NOT contain hyphens (blade-icons splits on first `-`)

## Architecture
- Content model: Collections → Blueprints → Entries (inspired by Statamic)
- Session driver: database

## Roles & Permissions
- Enum `App\Enums\UserRole` (SuperAdmin, Admin, Editor, Contributor)
- SuperAdmin bypasses all permissions via `Gate::before()` in AppServiceProvider
- Seeder: `RolesAndPermissionsSeeder` (idempotent, safe to re-run)
- Translations: `lang/cs/roles.php`, `lang/en/roles.php`

## Providers
- `AppServiceProvider` — Gate::before, LanguageSwitch config
- `IconServiceProvider` — FilamentIcon aliases (dashboard icon etc.)
- `AdminPanelProvider` — Filament panel config

## Conventions

### Code
- Write clean, readable code with type hints
- Use PHP 8.3+ features (enums, readonly properties, named arguments, match expressions)
- Follow PSR-12 coding standard
- Models in `app/Models/`, enums in `app/Enums/`
- Filament resources in `app/Filament/Resources/`
- All UI strings must be translated via `__()` or `trans()`
- Database columns and code in English, UI text in Czech with English fallback
- Run `vendor/bin/pint --dirty --format agent` after modifying PHP files
- Use `php artisan make:*` commands to create new files
- Always pass `--no-interaction` to Artisan commands
- Filament icons: use `Heroicon` enum or blade-icons string (e.g. `fal-house`)
- Translations: Czech (`cs`) is primary, English (`en`) secondary

### Testing
- Write corresponding tests after creating any new class, model, seeder, or service
- Tests go in `tests/Feature/` or `tests/Unit/` depending on nature
- Use Pest PHP (not PHPUnit syntax)
- Run each test and verify it passes: `php artisan test --filter=TestName`
- If a test fails, fix the code and re-run until it passes
- Test edge cases and validation

### Artisan Commands — run automatically after each relevant operation
- After migration change: `php artisan migrate`
- After seeder change: `php artisan db:seed --class=SeederName`
- After config change: `php artisan config:clear`
- After route change: `php artisan route:clear`
- After view change: `php artisan view:clear`
- After any major change: `php artisan optimize:clear`
- After Filament resource change: `php artisan filament:optimize-clear`
- After adding icons: `php artisan icons:clear && php artisan icons:cache`
- When in doubt: `php artisan optimize:clear`

### Git
- Commit after each logical unit with a descriptive message in English
- Format: `feat: add roles and permissions system`
- Types: feat, fix, refactor, test, chore, docs

## Workflow for Each Task
1. Read the assignment and make sure you understand it
2. Plan the implementation — which files to create/modify
3. Implement step by step
4. Run necessary artisan commands after each step
5. Write tests for new functionality
6. Run tests and verify they pass
7. If tests fail, fix and repeat
8. Commit with a descriptive message

## Do NOT
- Install new composer/npm packages without explicit instruction
- Modify Filament panel config (AdminPanelProvider) without instruction
- Modify existing migrations — create new ones instead
- Use Filament Shield — we have a custom role system
- Publish vendor views unless absolutely necessary
- Write "TODO" or "FIXME" comments — complete everything immediately

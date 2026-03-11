# miPress CMS

miPress je modulární CMS postavený na Laravel 12 a Filament 5. Projekt je navržený jako profesionální alternativa WordPressu pro český trh a míří hlavně na firemní prezentace, magazínové weby, menší obsahové projekty a komunitní weby.

## Přehled

- Admin panel běží na `/mpcp`
- Frontend používá CMS catch-all routing nad entries
- Obsahový model je inspirovaný Statamicem: Collections -> Blueprints -> Entries
- Lokalizace je databázově řízená přes model `Locale` a službu `LocaleService`
- Média řeší Filament Curator
- Blokový obsah řeší Mason

## Stack

### Backend

- PHP 8.3.x
- Laravel 12
- Filament 5
- Livewire 4
- MySQL 8

### Obsah a admin

- `spatie/laravel-permission`
- `awcodes/mason`
- `awcodes/filament-curator`
- `bezhansalleh/filament-language-switch`
- `jeffgreco13/filament-breezy`
- `caresome/filament-auth-designer`

### Frontend tooling

- Vite 7
- Tailwind CSS 4

### Testy a kvalita

- Pest 4
- Laravel Pint

## Lokální prostředí

Projekt je připravený pro Laravel Herd / Laragon. V aktuálním prostředí běží na:

- `https://mipresscz.test`

Admin panel:

- `https://mipresscz.test/mpcp`

## Instalace

### 1. Základní setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate --no-interaction
```

### 2. Databáze

Nastav připojení v `.env`, pak spusť:

```bash
php artisan migrate --no-interaction
php artisan db:seed --no-interaction
```

### 3. Assety

Vývoj:

```bash
npm run dev
```

Produkční build:

```bash
npm run build
```

### Nebo jedním příkazem

```bash
php artisan mipresscz:install --seed --no-interaction
```

## Nejčastější příkazy

```bash
php artisan optimize:clear
php artisan filament:optimize-clear
vendor/bin/pint --dirty --format agent
php artisan test --compact
```

Jednotlivé typy změn:

- po změně migrací: `php artisan migrate`
- po změně seedru: `php artisan db:seed --class=SeederName`
- po změně routes: `php artisan route:clear`
- po změně views: `php artisan view:clear`
- po větší změně: `php artisan optimize:clear`

## Architektura

Projekt je rozdělen na **core package** (`packages/mipresscz/core/`) a **tenkou app vrstvu** (`app/`).

- Core obsahuje veškerou CMS byznys logiku: modely, resources, services, policies, seeders
- App obsahuje pouze: `User` model, `UserRole` enum, `AdminPanelProvider` wrapper, Mason bricks

Podrobnosti: [docs/architecture-core.md](docs/architecture-core.md)

### Hlavní entity (v `packages/mipresscz/core/src/Models/`)

- `Collection`: typ obsahu a routing pravidla
- `Blueprint`: JSON definice polí pro kolekci
- `Entry`: obsahový záznam
- `Revision`: historie změn entry
- `Taxonomy` a `Term`: klasifikace a štítkování
- `GlobalSet`: globální obsahové sady
- `Locale`: jazyková konfigurace pro frontend i admin

### Klíčové principy

- ULID primární klíče na content modelech
- překlady přes samostatné entries a `origin_id`
- soft deletes na hlavních content modelech
- enumy pro stavy a vybraná nastavení
- blueprint pole uložená jako JSON

## Filament a admin architektura

### Struktura resources

Filament resources drží konzistentní pattern:

- `Resource.php`
- `Schemas/*Form.php`
- `Tables/*Table.php`
- `Pages/*`

### Dynamické entry resources

Entries nejsou jen jedna statická resource. Aktivní kolekce se načítají z databáze a `AdminPanelProvider` pro ně skládá dynamické konfigurace přes `EntryResourceConfiguration`.

Prakticky to znamená:

- každá aktivní kolekce má vlastní admin sekci,
- změny v kolekcích ovlivňují navigaci,
- úpravy entries musí počítat s kolekčně konfigurovanou resource variantou.

### Locale správa

Správa jazyků není klasická Resource, ale samostatná Filament Page v core.

## Lokalizace a routing

Frontend používá dva paralelní route režimy:

- prefixované URL: `/{locale}/{uri}`
- neprefixované URL: `/{uri}`

Locale logika je řízená z databáze přes:

- `MiPressCz\Core\Models\Locale`
- `MiPressCz\Core\Services\LocaleService`
- `MiPressCz\Core\Http\Middleware\SetFrontendLocale`

Aktuální chování:

- pokud je na frontendu aktivní více jazyků, používají se locale prefixy,
- pokud je aktivní jen jeden frontend jazyk, URL prefix se nepoužívá,
- v single-language režimu se prefixovaná URL přesměrovávají na neprefixovanou variantu.

## Média a blokový obsah

### Curator

Média v adminu jsou řešená přes Filament Curator. Pro veřejné použití je nutné vědomě řešit visibility a vztahy na `Awcodes\Curator\Models\Media`.

### Mason

Block builder obsah je uložený jako JSON struktura a renderuje se přes Mason bricky a blade views.

Admin theme CSS už obsahuje potřebné importy a source cesty pro oba pluginy v:

- `resources/css/filament/admin/theme.css`

## Testování

Projekt používá Pest 4.

### Důležité informace

- `tests/Pest.php` přidává `RefreshDatabase` pro Feature testy
- `phpunit.xml` používá MySQL databázi `mipresscz_testing`
- nepředpokládej SQLite-only chování

### Aktuálně pokryté oblasti

- content system
- entry routing
- locale workflow
- locale observer
- locale service
- roles a permissions

## Dokumentace

Autoritativní projektová dokumentace:

- [docs/architecture-core.md](docs/architecture-core.md) — architektura core vs. app
- [docs/project-analysis.md](docs/project-analysis.md) — analýza, technický dluh, matice kompletnosti
- [docs/project-roadmap.md](docs/project-roadmap.md) — fáze vývoje a plán
- [docs/contributing-core.md](docs/contributing-core.md) — workflow pro přispívání
- [docs/upgrade-guide.md](docs/upgrade-guide.md) — postup aktualizace mezi verzemi

Při rozporu mezi README a kódem ber jako zdroj pravdy dokumentaci v `docs/` a aktuální implementaci.

## Vývojové konvence

- používej PHP 8.3+ features a type hints
- všechny UI texty překládej přes `__()` nebo `trans()`
- databázové názvy a interní kód drž v angličtině, UI texty primárně v češtině
- po změně PHP souborů spusť `vendor/bin/pint --dirty --format agent`
- nové třídy a soubory zakládej přes `php artisan make:* --no-interaction`
- po architektonických změnách aktualizuj dokumentaci v `docs/`

## Poznámky pro onboarding

- CMS jádro žije v `packages/mipresscz/core/` — `app/Models/` obsahuje pouze `User.php`
- Zásahy do locale a routingu bývají průřezové a typicky vyžadují změny ve službě, middleware, URL generování, admin správě a testech.
- Zásahy do admin navigace a topbaru je potřeba dělat s respektem k existujícím render hookům a panel customizacím v `AdminPanelProvider`.

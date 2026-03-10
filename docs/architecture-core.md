# Architecture — mipresscz/core

## Přehled

`mipresscz/core` je CMS jádro extrahované do Composer balíčku (path repository). Aplikace (`mipresscz/`) je tenká vrstva, která core načítá a lokálně rozšiřuje.

```
mipresscz/
├── packages/mipresscz/core/   # CMS jádro (doména, resources, HTTP, seed/lang/views)
│   ├── composer.json
│   ├── CHANGELOG.md
│   ├── config/
│   ├── database/
│   │   ├── factories/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── resources/
│   │   ├── lang/              # cs/ + en/ překlady
│   │   └── views/             # entry šablony + fallback
│   ├── routes/
│   │   └── web.php            # core frontend routing (catch-all)
│   └── src/
│       ├── MiPressCzCoreServiceProvider.php
│       ├── helpers.php
│       ├── Console/Commands/  # mipresscz:install
│       ├── Enums/             # EntryStatus, DefaultStatus, DateBehavior
│       ├── Filament/          # Resources, Pages, Schemas, Tables
│       ├── Http/Controllers/  # EntryController
│       ├── Models/            # Collection, Blueprint, Entry, Taxonomy, Term,
│       │                      #   Revision, GlobalSet, Locale
│       ├── Observers/         # EntryObserver
│       ├── Policies/          # content policies (incl. TermPolicy)
│       ├── Providers/         # MiPressCzAdminPanelProvider
│       └── Services/          # LocaleService
│
└── app/                       # App-specifické věci
    ├── Providers/Filament/
    │   └── AdminPanelProvider.php   # extends MiPressCzAdminPanelProvider
    ├── Mason/                       # Custom bricks
    ├── Models/                      # User (auth model)
    ├── Http/Middleware/             # Custom middleware
    └── Filament/Resources/          # App-specific resource/page override
```

---

## Hranice core vs. app

| Patří do core | Patří do app |
|---|---|
| Content modely (Entry, Collection, Blueprint…) | `User` model (auth) |
| Locale, LocaleService, SetFrontendLocale | Brand assets, logo, favicon |
| Enums pro obsah a workflow | AuthDesigner customizace |
| EntryObserver, revize | App-specific Filament customizace |
| Content policies | Custom Mason bricks |
| Filament resources (Collections, Entries…) | Web-specific pages/resource override |
| EntryController, frontend routing | Custom middleware |
| Installer command | Panel color/branding override |
| Seedery, factories, migrace | — |
| Lang `cs`/`en`, shared views | — |

**Pravidlo:** pokud logika slouží obsahu (content workflow, routing, locale, revize), patří do core. Pokud slouží konkrétní aplikaci (branding, auth, custom bricks), patří do `app/`.

---

## Service provider

`MiPressCzCoreServiceProvider` v core zajišťuje:

- Registraci `LocaleService` jako singleton.
- Načtení core views (`resources/views` → namespace `mipresscz-core`).
- Přidání core lang cest do přepisovatelné hierarchie (app lang má přednost).
- Registraci všech content policies přes `Gate::policy()`.
- Načtení core frontend routes **po** appce (pomocí `$this->app->booted()`), aby app-specifické routy měly přednost.
- Registraci `mipresscz:install` artisan příkazu.
- Publikovací tagy: `mipresscz-migrations`, `mipresscz-config`, `mipresscz-views`, `mipresscz-translations`.

---

## Modely a relace

```
Collection 1──* Blueprint 1──* Entry
Collection 1──* Entry (přímá FK)
Entry *──* Entry               (entry_relationships — related entries)
Entry *──* Term                (termables — polymorfní pivot)
Entry 1──* Revision
Entry *──1 User               (author_id)
Entry 1──* Entry               (origin_id — locale překlady)
GlobalSet 1──* GlobalSet       (origin_id — locale překlady)
Taxonomy 1──* Term
Term 1──* Term                 (parent_id — hierarchie)
Taxonomy *──* Collection       (taxonomy_collection)
```

Všechny modely v core používají **ULID** jako primární klíč (`$incrementing = false`, `$keyType = 'string'`).

---

## Locale routing

Core registruje frontend routing ve dvou módech dle `LocaleService::isSingleLanguage()`:

| Mód | URL struktura | Chování |
|---|---|---|
| Single-language | `/{slug}` | Žádné locale prefixy; přímý match |
| Multi-language | `/{locale}/{slug}` | Locale prefix povinný; fallback na výchozí jazyk |

Redirect z prefixed URL na unprefixed v single-language módu zajišťuje `SetFrontendLocale` middleware.

---

## Admin panel

`MiPressCzAdminPanelProvider` (core) definuje základní konfiguraci Filament panelu (`/mpcp`). App `AdminPanelProvider` ji rozšiřuje a může přepsat:

- `configureBase()` — panel path, barvy, SPA
- `configureNavigation()` — navigační skupiny
- `configurePlugins()` — Curator, Breezy, LanguageSwitch
- `configureMiddleware()` — middleware stack
- `configureDiscovery()` — auto-discovery resources/pages

---

## Filament resources

Entry resources jsou dynamické — core registruje `EntryResourceConfiguration` per aktivní collection a `getCollectionResources()` v `AdminPanelProvider` je předává do panelu. Každá collection má vlastní resource instanci s prefixovanou navigací.

---

## Jak core rozšiřovat

### Přidat nový model do app vrstvy

1. Vytvořit model v `app/Models/` standardním způsobem.
2. Pro vztah na core modely používat importy z `MiPressCz\Core\Models\`.
3. Factory a seeder jdou do `database/factories/` a `database/seeders/` v root projektu.

### Přepsat core view

Publikovat views tagem `mipresscz-views`:

```bash
php artisan vendor:publish --tag=mipresscz-views
```

Poté upravit kopie v `resources/views/vendor/mipresscz-core/`.

### Přepsat core config

```bash
php artisan vendor:publish --tag=mipresscz-config
```

### Přidat custom Mason brick

Vytvořit třídu v `app/Mason/` a view v `resources/views/mason/`:

```bash
php artisan make:mason-brick BrickName
```

Brick zaregistrovat v `AdminPanelProvider::configurePlugins()` nebo v konfiguraci Mason fieldu.

### Přepsat Filament resource

Vytvořit resource v `app/Filament/Resources/` se stejným názvem. Core resource bude stále registrovaný — pokud je třeba ho deaktivovat, deaktivovat discovery v `AdminPanelProvider::configureDiscovery()`.

---

## Verzování

Core sleduje SemVer (`MAJOR.MINOR.PATCH`):

- `PATCH` — opravy chyb, bezpečnostní záplaty
- `MINOR` — nové funkce, zpětně kompatibilní
- `MAJOR` — breaking changes (viz `docs/upgrade-guide.md`)

Aktuální verze: `0.6.0` (pre-release; API se může změnit před `1.0.0`).

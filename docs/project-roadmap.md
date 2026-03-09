# miPress CMS — Project Roadmap

Datum: 9. března 2026

## Strategický cíl

Dostat `mipresscz` do stavu **„product-ready core + tenká app vrstva"** jako `mipress`, s rozumnou inspirací z `tallcms` (modularita, provider/plugin pattern, release disciplína), ale bez zbytečného over-engineeringu.

Referenční projekty:

- `mipress` — target state (monorepo, core package, extensible panel provider, installer)
- `tallcms` — inspirace pro package-first disciplínu, modular toggle, release governance

---

## Architektonický target

```
mipresscz/
├── packages/mipresscz/core/        # CMS jádro (doména, resources, HTTP, seed/lang/views)
│   ├── composer.json
│   ├── config/
│   ├── database/migrations/
│   ├── database/factories/
│   ├── database/seeders/
│   ├── resources/lang/
│   ├── resources/views/
│   └── src/
│       ├── MiPressCzCoreServiceProvider.php
│       ├── MiPressCzCorePlugin.php
│       ├── Providers/Filament/MiPressCzAdminPanelProvider.php
│       ├── Models/                  # Collection, Blueprint, Entry, Taxonomy, Term,
│       │                            #   Revision, GlobalSet, Locale
│       ├── Enums/                   # EntryStatus, DefaultStatus, DateBehavior
│       ├── Services/                # LocaleService, URL resolution
│       ├── Observers/               # EntryObserver
│       ├── Policies/                # content-related policies
│       ├── Filament/Resources/      # Collections, Entries, Taxonomies, Globals, Terms
│       ├── Http/Controllers/        # EntryController, případně PreviewController
│       ├── Console/Commands/        # mipresscz:install
│       └── helpers.php
│
├── app/                             # Jen app-specific věci
│   ├── Providers/Filament/
│   │   └── AdminPanelProvider.php   # extends MiPressCzAdminPanelProvider
│   ├── Mason/                       # custom bricks
│   ├── Filament/Resources/          # app-specific resources/pages override
│   ├── Http/Middleware/             # custom middleware
│   └── Models/User.php              # app-level auth model
│
├── resources/
│   └── assets/                     # brand assets, logo, favicon
└── ...
```

### Co patří do core vs. app

| Core | App |
|------|-----|
| Content modely (Entry, Collection, Blueprint…) | `User` model (auth) |
| Locale, LocaleService, SetFrontendLocale | Brand assets, logo, favicon |
| Enums pro obsah a workflow | AuthDesigner customize |
| EntryObserver, revize | App-specific Filament customizations |
| Policies pro obsah | Custom Mason bricks |
| Filament resources (Collections, Entries…) | Web-specific pages/resource override |
| EntryController, frontend routing | Custom middleware |
| Installer command | Panel color/branding override |
| Seedery, factories, migrace | — |
| Lang `cs`/`en`, shared views | — |

---

## Fáze 0 — Freeze a baseline
**Odhad: 1–2 dny**

- [ ] Vytvořit `refactor/core-extraction` branch.
- [ ] Zapsat baseline metriky (test pass rate, počet migrací/modelů/resources, URL scénáře).
- [ ] Přidat ADR dokument: hranice core/app.
- [ ] Zmrazit nové feature PR na dobu extrakce.

**Výstup:** jasný start, bezpečná izolace refactoru.

---

## Fáze 1 — Vytvoření `mipresscz/core` balíčku
**Odhad: 2–3 dny**

- [ ] Vytvořit `packages/mipresscz/core` s `composer.json` (type: library).
- [ ] Přidat `MiPressCzCoreServiceProvider` přes `spatie/laravel-package-tools`.
- [ ] Definovat publish tags: `mipresscz-config`, `mipresscz-views`, `mipresscz-translations`, `mipresscz-migrations`.
- [ ] Přesunout `app/helpers.php` do `packages/mipresscz/core/src/helpers.php`.
- [ ] Přidat package do root `composer.json` jako path repository.
- [ ] Ověřit boot aplikace, spustit testy.

**Výstup:** funkční core package načtený appkou.

---

## Fáze 2 — Panel Provider pattern
**Odhad: 2 dny**

- [ ] V core vytvořit `MiPressCzAdminPanelProvider` s hook metodami:
  - `configureBase()` — path, colors, SPA, appearance
  - `configureNavigation()` — navigation groups
  - `configurePlugins()` — Curator, Breezy, language switch
  - `configureMiddleware()` — middleware stack
  - `configureDiscovery()` — resource/page auto-discovery
- [ ] App provider zredukovat na:
  ```php
  class AdminPanelProvider extends MiPressCzAdminPanelProvider {}
  ```
- [ ] Přesunout Curator, Breezy, AuthDesigner config do core (s možností override v app).
- [ ] Ověřit boot, spustit testy.

**Výstup:** čisté oddělení panel konfigurace, app provider je prázdná vrstva.

---

## Fáze 3 — Extrakce doménového jádra
**Odhad: 5–8 dní**

### Modely
- [ ] `Collection`, `Blueprint`, `Entry`, `Taxonomy`, `Term`, `Revision`, `GlobalSet`, `Locale`
- [ ] Udržet ULID, HasRoles, SoftDeletes, všechny relationships

### Enums
- [ ] `EntryStatus`, `DefaultStatus`, `DateBehavior`, `UserRole` (obsah část)

### Services a Observers
- [ ] `LocaleService`, URL resolution helpery
- [ ] `EntryObserver` (revize, pruning)

### Policies
- [ ] `CollectionPolicy`, `EntryPolicy`, `TaxonomyPolicy`, `BlockPolicy`, `GlobalSetPolicy`

### Filament Resources
- [ ] Collections, Entries (dynamic ResourceConfiguration), Taxonomies, Terms, Globals

### HTTP a routing
- [ ] `EntryController` (frontend rendering)
- [ ] Základní frontend route registrace v core

### Seedery a factories
- [ ] `RolesAndPermissionsSeeder`, `ContentSeeder`, `GlobalsSeeder`, `LocaleSeeder`
- [ ] Všechny content factories

### Lang a views
- [ ] `lang/cs`, `lang/en` content překlady
- [ ] `resources/views/entries/`, fallback views

**Výstup:** core nese byznys logiku, app je tenká vrstva.

---

## Fáze 4 — Routing a locale kontrakt
**Odhad: 2–4 dny**

- [ ] Definovat routing kontrakt v core:
  - locale prefix režim (více jazyků)
  - single-language mode bez prefixu
  - redirect prefixed → unprefixed v single mode
- [ ] App `routes/web.php` ztenčit na include core + app-specific override.
- [ ] Napsat explicitní routing test matrix pro locale scénáře.

**Výstup:** stabilní i18n routing kontrakt bez driftu.

---

## Fáze 5 — Installer command
**Odhad: 2–3 dny**

- [ ] Vytvořit `php artisan mipresscz:install` v core s volbami:
  - `--admin-name`, `--admin-email`, `--admin-password`
  - `--force` (přeinstalování)
  - `--seed` (demo obsah)
- [ ] Installer zajistí:
  - migrace
  - role/permission setup
  - výchozí Collection + Blueprint
  - locale init
  - admin user
- [ ] Otestovat instalaci na čisté databázi.

**Výstup:** reprodukovatelné nasazení v jednom příkazu.

---

## Fáze 6 — Test parity
**Odhad: 4–6 dní**

- [ ] Zachovat Pest jako primární test framework (žádný mix PHPUnit stylu).
- [ ] Přidat kontraktní testy pro core modely (relationships, scopes, factories).
- [ ] Přidat testy pro LocaleService a routing matrix.
- [ ] Přidat policy matrix testy (kdo může co s jakým obsahem).
- [ ] Přidat integration testy app vrstvy (panel boot, dynamic resources, middleware).
- [ ] Dosáhnout alespoň parity s dnešním stavem (83+ Pest definic), ideálně výrazně přes 150.

**Výstup:** bezpečný refactor potvrzený testy, žádné nevykryté regrese.

---

## Fáze 7 — Release a governance
**Odhad: 2–3 dny**

- [ ] Semver verzování v `packages/mipresscz/core/composer.json`.
- [ ] `CHANGELOG.md` pro core.
- [ ] CI: lint (Pint), testy, install smoke test.
- [ ] Dokumentace:
  - `docs/architecture-core.md` — hranice core/app, jak core rozšiřovat
  - `docs/upgrade-guide.md` — versioning a breaking changes
  - `docs/contributing-core.md` — workflow pro přispívání do core

**Výstup:** dlouhodobě udržitelný model s jasnou governance.

---

## Prioritní To-Do backlog

### P0 — Hned (Fáze 0–2)
- Vytvořit `packages/mipresscz/core` skeleton + provider
- Zavést dědění `AdminPanelProvider` z core
- Přesunout content modely + services + observers
- Přesunout základní Filament resources do core
- Udržet všechny existující testy zelené

### P1 — Krátce (Fáze 3–4)
- Installer command
- Core route registrace + locale routing kontrakt
- Přesun seeders/factories/lang/views

### P2 — Střednědobě (Fáze 5–6)
- API vrstva do core (headless/REST)
- `mipresscz:new-site` command
- Publish tags a dokumentace pro integrátory

### P3 — Dlouhodobě (Fáze 7+)
- `withoutX()` selective toggle pattern (po vzoru `tallcms`)
- Optional plugin manager-like registry
- Optional class alias compat layer pro budoucí distribuci

---

## Co převzít z `tallcms` a co ne

| Převzít | Nepřebírat teď |
|---------|----------------|
| Package-first disciplína | Full standalone/plugin dual-mode |
| Čistý service provider bootstrapping | Rozsáhlý alias compat layer |
| Modular toggle pro features | Marketplace infrastruktura |
| Release docs + changelog | Pro-feature ekosystém |
| `withoutX()` pattern (Fáze P3) | — |

---

## Definition of Done

- `mipresscz` běží na `packages/mipresscz/core`
- `AdminPanelProvider` v app je tenká dědická vrstva
- Core obsahuje všechny CMS-common součásti
- Installer funguje na čisté instalaci
- Kritické testy pro content + locale + routing + policy jsou zelené
- `docs/architecture-core.md` jasně dokumentuje hranici core/app

---

## Rozhodovací pravidla

1. Každé rozhodnutí „kam to patří" vždy v kontextu: bude to potřeba na jiném webu? → core. Jen pro tento web? → app.
2. Nejdřív přesunout, pak refactorovat — nezlepšovat přesouvaný kód zároveň s přesunem.
3. Testy musí být zelené po každé fázi, ne jen na konci.
4. Neduplikovat logiku — pokud je v core, app ji neimplementuje znovu.

---

## Poznámka k údržbě

Po dokončení každé fáze: aktualizovat tento dokument (zaškrtnout `[ ]` → `[x]`) a doplnit datum. Roadmapa není statický dokument, ale živý plán.

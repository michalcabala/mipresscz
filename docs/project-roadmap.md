# miPress CMS — Project Roadmap

Datum: 10. března 2026

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

## Fáze 0 — Freeze a baseline ✅
**Dokončeno: 9. března 2026**

- [x] Vytvořit `refactor/core-extraction` branch.
- [x] Zapsat baseline metriky (test pass rate, počet migrací/modelů/resources, URL scénáře).
- [x] Přidat ADR dokument: hranice core/app.
- [x] Zmrazit nové feature PR na dobu extrakce.

**Výstup:** jasný start, bezpečná izolace refactoru.

---

## Fáze 1 — Vytvoření `mipresscz/core` balíčku ✅
**Dokončeno: 9. března 2026**

- [x] Vytvořit `packages/mipresscz/core` s `composer.json` (type: library).
- [x] Přidat `MiPressCzCoreServiceProvider` přes `spatie/laravel-package-tools`.
- [x] Definovat publish tags: `mipresscz-config`, `mipresscz-views`, `mipresscz-translations`, `mipresscz-migrations`.
- [x] Přesunout `app/helpers.php` do `packages/mipresscz/core/src/helpers.php`.
- [x] Přidat package do root `composer.json` jako path repository.
- [x] Ověřit boot aplikace, spustit testy.

**Výstup:** funkční core package načtený appkou.

---

## Fáze 2 — Panel Provider pattern ✅
**Dokončeno: 9. března 2026**

- [x] V core vytvořit `MiPressCzAdminPanelProvider` s hook metodami:
  - `configureBase()` — path, colors, SPA, appearance
  - `configureNavigation()` — navigation groups
  - `configurePlugins()` — Curator, Breezy, language switch
  - `configureMiddleware()` — middleware stack
  - `configureDiscovery()` — resource/page auto-discovery
- [x] App provider zredukovat na:
  ```php
  class AdminPanelProvider extends MiPressCzAdminPanelProvider {}
  ```
- [x] Přesunout Curator, Breezy, AuthDesigner config do core (s možností override v app).
- [x] Ověřit boot, spustit testy.

**Výstup:** čisté oddělení panel konfigurace, app provider je prázdná vrstva.

---

## Fáze 3 — Extrakce doménového jádra ✅
**Dokončeno: 9. března 2026**

### Modely
- [x] `Collection`, `Blueprint`, `Entry`, `Taxonomy`, `Term`, `Revision`, `GlobalSet`, `Locale`
- [x] Udrzet ULID, SoftDeletes, všechny relationships; wrapper modely z `app/Models/` smazány

### Enums
- [x] `EntryStatus`, `DefaultStatus`, `DateBehavior` v core; `UserRole` v app (auth-specific)

### Services a Observers
- [x] `LocaleService`, URL resolution helpery
- [x] `EntryObserver` (revize, pruning) — registrace přesunuta do core SP

### Policies
- [x] `CollectionPolicy`, `EntryPolicy`, `TaxonomyPolicy`, `BlueprintPolicy`, `GlobalSetPolicy`; registrace výlučně v core SP

### Filament Resources
- [x] Collections, Entries (dynamic ResourceConfiguration), Taxonomies, Terms, Globals

### HTTP a routing
- [x] `EntryController` (frontend rendering)
- [x] Základní frontend route registrace v core

### Seedery a factories
- [x] `RolesAndPermissionsSeeder`, `ContentSeeder`, `GlobalsSeeder`, `LocaleSeeder`
- [x] Všechny content factories v core; duplikátní app factories smazány

### Lang a views
- [x] `lang/cs`, `lang/en` content překlady
- [x] `resources/views/entries/`, fallback views

**Výstup:** core nese byznys logiku, app je tenká vrstva.

---

## Fáze 4 — Routing a locale kontrakt ✅
**Dokončeno: 9. března 2026**

- [x] Definovat routing kontrakt v core:
  - locale prefix režim (více jazyků)
  - single-language mode bez prefixu
  - redirect prefixed → unprefixed v single mode
- [x] App `routes/web.php` ztenčit na include core + app-specific override.
- [x] Napsat explicitní routing test matrix pro locale scénáře.

**Výstup:** stabilní i18n routing kontrakt bez driftu.

---

## Fáze 5 — Installer command ✅
**Dokončeno: 9. března 2026**

- [x] Vytvořit `php artisan mipresscz:install` v core s volbami:
  - `--admin-name`, `--admin-email`, `--admin-password`
  - `--force` (přeinstalovaní)
  - `--seed` (demo obsah)
- [x] Installer zajiští:
  - migrace
  - role/permission setup
  - výchozí Collection + Blueprint
  - locale init
  - admin user
- [x] Otestovat instalaci na čisté databázi.

**Výstup:** reprodukovatelné nasazení v jednom příkazu.

---

## Fáze 6 — Test parity ✅
**Dokončeno: 9. března 2026**

- [x] Zachovat Pest jako primární test framework (žádný mix PHPUnit stylu).
- [x] Přidat kontraktní testy pro core modely (relationships, scopes, factories).
- [x] Přidat testy pro LocaleService a routing matrix.
- [x] Přidat policy matrix testy (kdo může co s jakým obsahem).
- [x] Přidat integration testy app vrstvy (panel boot, dynamic resources, middleware).
- [x] Dosáhnout alespoň parity s dnešním stavem (83+ Pest definic), ideálně výrazně přes 150. → **185 definic**

**Výstup:** bezpečný refactor potvrzený testy, žádné nevykryté regrese.

---

## Fáze 7 — Stabilizace a hardening ⏳
**Zahájeno: 10. března 2026**

Cíl: uzavřít autorizační mezery, zvýšit testové pokrytí admin panelu a vyčistit redundance.

### 7.1 — Autorizace a bezpečnost ✅

- [x] Vytvořit `TermPolicy` — uzavření autorizační mezery (Terms nemají policy)
- [x] Registrovat TermPolicy v `MiPressCzCoreServiceProvider`
- [x] Test matrix pro TermPolicy (kdo může co s termy) — 16 testů

### 7.2 — Testové pokrytí admin panelu (částečně ✅)

- [x] Filament resource testy — CollectionResource (9), TaxonomyResource (10), GlobalSetResource (9), BlueprintResource (8)
- [x] Dynamic resource creation test — ověření `getCollectionResources()` — EntryResourceConfigTest (5)
- [ ] Filament form testy — EntryResource CRUD (create, edit, validate) — složitější kvůli Mason + dynamic resources
- [ ] Filament table testy — filtry, řazení, bulk akce
- [ ] Mason brick snapshot testy — HTML výstup 13 bricks
- [ ] Curator media testy — featured image, galerie workflow

### 7.2b — Bug fixy nalezené při testování ✅

- [x] `?string $state` nullable oprava v 7 form schématech (CollectionForm, TaxonomyForm, TermForm, BlueprintForm, GlobalSetForm, EntryForm, BlueprintsRelationManager)
- [x] `mutateFormDataBeforeCreate()` — chybějící `name = handle` v 4 Create stránkách (CreateCollection, CreateBlueprint, CreateGlobalSet, BlueprintsRelationManager)

### 7.3 — Čištění redundancí ✅

- [x] Audit language switcher balíčků — `craft-forge/filament-language-switcher` odstraněn, ponechán `bezhansalleh/filament-language-switch`
- [x] Přeložit Breezy (2FA) strings do češtiny — `lang/vendor/filament-breezy/cs/default.php`
- [x] Přepsat README.md — nahradit default Laravel placeholder odkazem na `docs/`

### 7.4 — Příprava na merge

- [ ] Merge `refactor/core-extraction` → `main`
- [ ] `npm run build` + ověření Vite manifestu
- [ ] Tagovat `v0.7.0`

**Aktuální stav:** 226 testů (41 nových), všechny zelené. Zbývají pokročilé testy (7.2) a merge (7.4).

**Výstup:** bezpečný, čistý codebase připravený pro vývoj frontend šablony.

---

## Fáze 8 — SEO & Discovery (plánováno)

- [ ] Meta tagy v Entry UI — title, description, og:image
- [ ] Hreflang tagy — automaticky z locale vazeb
- [ ] Sitemap generátor — XML sitemap z published entries
- [ ] RSS/Atom feed — pro články
- [ ] Canonical URL — prevence duplicitního obsahu

---

## Fáze 9 — Funkční rozšíření (plánováno)

- [ ] Fulltext vyhledávání — Laravel Scout + database driver
- [ ] Admin dashboard widgety — počty entries, poslední aktivita
- [ ] Menu builder — drag & drop navigační struktura
- [ ] Entry preview — náhled před publikací
- [ ] Media tagging/folders — organizace Curator médií

---

## Fáze 10 — Produkční připravenost (plánováno)

- [ ] Caching strategie — page cache, entry cache invalidace
- [ ] Error pages — 404, 500, maintenance mode
- [ ] Security hardening — CSP headers, rate limiting
- [ ] CI/CD pipeline — GitHub Actions pro testy + Pint
- [ ] Uživatelská dokumentace — admin UX guide

---

## Fáze 7 — Release a governance ✅
**Dokončeno: 9. března 2026**

- [x] Semver verzování v `packages/mipresscz/core/composer.json` (verze `0.6.0`).
- [x] `CHANGELOG.md` pro core (`packages/mipresscz/core/CHANGELOG.md`).
- [x] CI: `.github/workflows/ci.yml` — lint (Pint), testy (MySQL), install smoke test.
- [x] Dokumentace:
  - `docs/architecture-core.md` — hranice core/app, jak core rozšiřovat
  - `docs/upgrade-guide.md` — versioning a breaking changes
  - `docs/contributing-core.md` — workflow pro přispívání do core

**Výstup:** dlouhodobě udržitelný model s jasnou governance.

---

## Prioritní To-Do backlog

### ✅ Hotovo — Jádro extrahováno (Fáze 0–7, 9. března 2026)

Všechny plánované fáze dokončeny. Core extraction je **KOMPLETNÍ**.

- [x] `packages/mipresscz/core` s kompletní doménovou logikou
- [x] `AdminPanelProvider` dědí z `MiPressCzAdminPanelProvider`
- [x] Všechny content modely, services, observers, policies v core
- [x] Filament resources v core
- [x] Installer command (`php artisan mipresscz:install`)
- [x] Routing + locale kontrakt v core
- [x] Seeders/factories/lang/views v core
- [x] 185 testů zelených
- [x] Release governance (semver, changelog, CI, docs)

### P2 — Střednědobě
- API vrstva do core (headless/REST)
- `mipresscz:new-site` command
- Publish tags a dokumentace pro integrátory

### P3 — Dlouhodobě
- `withoutX()` selective toggle pattern (po vzoru `tallcms`)
- Optional plugin manager-like registry
- Optional class alias compat layer pro budoucí distribuci

### Otevřené technical debt položky

- ✅ ~~`TermPolicy` chybí~~ — vyřešeno 10. března 2026
- ✅ ~~Breezy překlady~~ — vyřešeno 10. března 2026
- 🟢 Uncached Filament assets — `php artisan icons:cache && php artisan filament:cache-components`

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

- [x] `mipresscz` běží na `packages/mipresscz/core`
- [x] `AdminPanelProvider` v app je tenká dědická vrstva
- [x] Core obsahuje všechny CMS-common součásti
- [x] Installer funguje na čisté instalaci
- [x] Kritické testy pro content + locale + routing + policy jsou zelené (226/226)
- [x] `docs/architecture-core.md` jasně dokumentuje hranici core/app

**Stav: Definition of Done splněno. ✅**

---

## Rozhodovací pravidla

1. Každé rozhodnutí „kam to patří" vždy v kontextu: bude to potřeba na jiném webu? → core. Jen pro tento web? → app.
2. Nejdřív přesunout, pak refactorovat — nezlepšovat přesouvaný kód zároveň s přesunem.
3. Testy musí být zelené po každé fázi, ne jen na konci.
4. Neduplikovat logiku — pokud je v core, app ji neimplementuje znovu.

---

## Poznámka k údržbě

Po dokončení každé fáze: aktualizovat tento dokument (zaškrtnout `[ ]` → `[x]`) a doplnit datum. Roadmapa není statický dokument, ale živý plán.

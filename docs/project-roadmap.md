# miPress CMS — Project Roadmap

Datum: 12. března 2026

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

### 7.2 — Testové pokrytí admin panelu ✅

- [x] Filament resource testy — CollectionResource (9), TaxonomyResource (10), GlobalSetResource (9), BlueprintResource (8)
- [x] Dynamic resource creation test — ověření `getCollectionResources()` — EntryResourceConfigTest (5)
- [x] EntryResource CRUD testy — create, edit, validate, publish/unpublish, delete, bulk delete (17)
- [x] Filament table testy — filtry status/locale, řazení, vyhledávání (součást EntryResourceTest)
- [x] Mason brick testy — HTML výstup 12 bricks + BrickCollection (16)
- [x] Curator media testy — featured image relationship, form integration, table column (6)
- [x] Opravena slug `unique` validace v EntryForm (Rule::unique s Get closure pro collection_id + locale)

### 7.2b — Bug fixy nalezené při testování ✅

- [x] `?string $state` nullable oprava v 7 form schématech (CollectionForm, TaxonomyForm, TermForm, BlueprintForm, GlobalSetForm, EntryForm, BlueprintsRelationManager)
- [x] `mutateFormDataBeforeCreate()` — chybějící `name = handle` v 4 Create stránkách (CreateCollection, CreateBlueprint, CreateGlobalSet, BlueprintsRelationManager)

### 7.3 — Čištění redundancí ✅

- [x] Audit language switcher balíčků — `craft-forge/filament-language-switcher` odstraněn, ponechán `bezhansalleh/filament-language-switch`
- [x] Přeložit Breezy (2FA) strings do češtiny — `lang/vendor/filament-breezy/cs/default.php`
- [x] Přepsat README.md — nahradit default Laravel placeholder odkazem na `docs/`

### 7.4 — Příprava na merge

- [x] Merge `refactor/core-extraction` → `main` *(11. března 2026)*
- [x] `npm run build` + ověření Vite manifestu
- [x] Tagovat `v0.7.0`

### 7.5 — Admin UI & Entry formulář ✅
**Dokončeno: 11. března 2026**

Cíl: Entry formulář musí vizuálně fungovat — správné rozvržení, dynamická pole dle blueprintu.

#### Layout fix
- [x] Diagnostika: `Flex` a `Grid` jako wrapper nefungují správně ve Filament 5
- [x] Správný pattern: `Group::make()->columnSpan(['lg' => 2/1])` + `$schema->columns(3)`
- [x] Namespace oprava: `Filament\Schemas\Components\Group` (ne `Filament\Forms\Components\Group`)
- [x] Odstraněn `getMaxContentWidth(): Width::Full` z `EditEntry` a `CreateEntry`

#### Blueprint dynamic fields fix
- [x] Diagnostika: `$get('blueprint_id')` nespolehlivé cross-Group closures ve Filament 5
- [x] Řešení: typovaná injekce `?Entry $record` v `schema(fn (?Entry $record): array => ...)`
- [x] `resolveDefaultBlueprintId()` — statická cache pro CreateRecord kontext (bez `$record`)
- [x] Podpora nového field type `entries` — `Select::multiple()` s Entry options z linked collections
- [x] Mason vždy viditelný — odstraněna podmínka `->visible(fn() => count($brickClasses) > 0)`

#### EntryForm architektura (výsledný stav)
```
$schema->columns(3)
├── Group(lg:2) — hlavní obsah
│   ├── Section: title, slug, Mason
│   └── Section: extra_fields (dynamic main blueprint fields)
├── Group(lg:1) — sidebar
│   ├── Section: featured_image, author, published_at, parent_id, is_pinned
│   └── Section: metadata (dynamic sidebar blueprint fields)
└── Hidden: collection_id, blueprint_id, locale, status, order
```

**Výstup:** Entry formulář plně funkční, dynamická blueprintová pole se správně načítají ze záznamu.

**Aktuální stav:** 291 testů (48 nových od Fáze 7.4), všechny zelené. Fáze 7.2 kompletní.

**Výstup:** bezpečný, čistý codebase připravený pro vývoj frontend šablony; Entry formulář plně funkční.

---

## Fáze 8 — SEO & Discovery ✅ (dokončeno — 307 testů)

- [x] Meta tagy v Entry UI — `meta_title`, `meta_description`, `meta_og_image_id` (migrace + EntryForm sidebar SEO sekce)
- [x] Hreflang tagy — automaticky z locale vazeb (`translations` / `origin.translations`)
- [x] Sitemap generátor — `GET /sitemap.xml` z published entries (`SitemapController`)
- [x] RSS/Atom feed — `GET /feed.xml` pro aktuální locale (`FeedController`)
- [x] Canonical URL — `<link rel="canonical">` v `show.blade.php` + `$canonicalUrl` z `EntryController`

---

## Fáze 9 — Funkční rozšíření (probíhá)

- [x] Admin dashboard widgety — počty entries, poslední aktivita (323 tests)
- [x] **[Statamic]** Lifecycle event páry `EntrySaving`/`EntrySaved` — cancelovatelné pre-eventy (323 tests)
- [x] Výchozí jazyk nelze smazat — `DeleteAction` skrytý pro `is_default` locale
- [x] Nastavení domovské stránky — akce `set_homepage`, ochrana homepage entry před smazáním
- [x] Soft deletes pro uživatele — obnovení, fyzické smazání, ochrana vlastního účtu (338 tests)
- [x] Menu builder — drag & drop navigační struktura *(P1)* (369 tests)
- [x] **Multijazyčné kolekce, blueprinty, taxonomie** — Terms per-locale záznamy (locale + origin_id self-FK, unikátní slug per locale), `HasLocalizedTitle` trait pro Collection + Taxonomy (`translations` JSON), dynamické Tabs per locale v CollectionForm + TaxonomyForm *(P1)* (362 tests)
- [ ] Entry preview — náhled před publikací *(P2)*
- [ ] Fulltext vyhledávání — Laravel Scout + database driver *(P3)*
- [ ] Media tagging/folders — organizace Curator médií *(P4)*
- [ ] **[Statamic]** `HasOrigin` trait — centralizovaný i18n fallback pro Entry lokalizaci *(P5)*
- [ ] **[Statamic]** Blink request-level cache — N+1 prevence pro origin/locale lookups *(P6)*

---

## Fáze 10 — Produkční připravenost (plánováno)

- [ ] Error pages — 404, 500, maintenance mode *(P1)*
- [ ] Security hardening — CSP headers, rate limiting *(P2)*
- [ ] CI/CD pipeline — GitHub Actions pro testy + Pint *(P3)*
- [ ] Caching strategie — page cache, entry cache invalidace *(P4)*
- [ ] Uživatelská dokumentace — admin UX guide *(P5)*
- [ ] **[Statamic]** `ContainsComputedData` — virtuální pole (word_count, reading_time, derived slug) *(P6)*

---

## Fáze 11 — Editorial workflow (plánováno)

- [ ] Working Copy — pracovní kopie entry odděleně od publikované verze
- [ ] `publishWorkingCopy()` / `deleteWorkingCopy()` API na Entry modelu
- [ ] CP UI pro schvalování a zamítnutí pracovní kopie (Editor → Admin workflow)
- [ ] Integrovat s rolemi: Contributor může pouze uložit do working copy, ne publikovat přímo

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

### ✅ Hotovo — Fáze 7 stabilizace + merge (11. března 2026)

- [x] `refactor/core-extraction` → `main` merge, tag `v0.7.0`
- [x] 243 testů zelených (oprava LocaleServiceTest izolace + TermFactory unique slug)
- [x] `npm run build` — Vite build čistý, 4 assety OK

### ✅ Hotovo — Fáze 8 + Fáze 9 první sada (11. března 2026)

- [x] Fáze 8: sitemap, RSS/Atom feed, hreflang, meta tagy, canonical URL — 307 testů
- [x] Fáze 9: admin dashboard widgety, lifecycle event páry `EntrySaving`/`EntrySaved` — 323 testů
- [x] Fáze 9: výchozí jazyk nelze smazat, nastavení domovské stránky, soft deletes pro uživatele — 338 testů

### ✅ Hotovo — Fáze 9 multijazyčné kolekce/taxonomie (12. března 2026)

- [x] Terms: per-locale záznamy — `locale` (string, default `cs`), `origin_id` (nullable self-FK s `nullOnDelete`), unique constraint změněn z `(taxonomy_id, slug)` → `(taxonomy_id, locale, slug)`
- [x] `HasLocalizedTitle` trait — `getLocalizedTitle(?locale)` + `getLocalizedDescription(?locale)` s fallback na `$this->title` / `$this->description`
- [x] Collection + Taxonomy modely: `translations` JSON sloupec (cast `array`), trait registrován
- [x] TermForm: `locale` select, `origin_id` select (filtráno dle taxonomy + výchozí locale), slug unique validace per locale, `parent_id` filtrován dle locale
- [x] TermsTable: locale badge sloupec + SelectFilter
- [x] CollectionForm + TaxonomyForm: dynamické Tabs per aktivní locale (`locales()->getActive()`)
- [x] Idempotentní migrace přepsána na `Schema::getForeignKeys()` / `Schema::getIndexes()` (spolehlivá i v test env)
- [x] 362 testů zelených (16 nových: MultilingualTermTest + LocalizedTitleTest)

### P1 — Nejbližší (Fáze 9 dokončení)
- ~~Menu builder — drag & drop navigační struktura~~ ✅ dokončeno
- ~~Multijazyčné kolekce/taxonomie~~ ✅ dokončeno (12. března 2026)
- Entry preview — náhled před publikací
- Fulltext vyhledávání — Laravel Scout + database driver
- Media tagging/folders — organizace Curator médií
- **[Statamic]** `HasOrigin` trait + Blink cache

### P2 — Střednědobě
- API vrstva do core (headless/REST)
- `mipresscz:new-site` command
- Publish tags a dokumentace pro integrátory
- **[Statamic]** `ContainsComputedData` pro vypočítaná pole (Fáze 10)
- ~~**[Statamic]** Lifecycle event páry pro Entry~~ ✅ dokončeno (323 tests)

### P3 — Dlouhodobě
- `withoutX()` selective toggle pattern (po vzoru `tallcms`)
- Optional plugin manager-like registry
- Optional class alias compat layer pro budoucí distribuci
- **[Statamic]** Working Copy editační workflow (Fáze 11)
- **[Statamic]** `Hookable` Pipeline pro addon/plugin rozšiřitelnost

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

## Co převzít ze `Statamic` — inspirace kódem

Statamic CMS byl přidán do workspace jako referenční codebase (`c:/laragon/www/statamic`). Analýza proběhla 11. března 2026.

| Inspirace | Priorita | Cílová fáze |
|-----------|----------|-------------|
| `HasOrigin` — automatický i18n fallback na hodnoty z origin entry | ★★★★★ | Fáze 9 |
| Lifecycle event páry `*Saving` / `*Saved` (cancelovatelný pre-event) | ★★★★☆ | ✅ Hotovo |
| Working Copy — pracovní kopie odděleně od publikované verze | ★★★★☆ | Fáze 11 |
| `ContainsComputedData` — virtuální/vypočítaná pole na modelu | ★★★☆☆ | Fáze 10 |
| Blink request-level cache — prevence N+1 pro origin lookups | ★★★☆☆ | Fáze 9 |
| `Hookable` Pipeline — rozšiřitelné hooky pro addon systém | ★★☆☆☆ | Fáze P3 |
| `FluentlyGetsAndSets` — fluent getter/setter pattern na modelech | ★★☆☆☆ | Fáze P3 |

### Detaily

**`HasOrigin` fallback (nejvyšší priorita)**
Statamic trait `HasOrigin` řeší překlady elegantně: lokalizovaný entry má `origin`, a při chybějícím poli automaticky fallbackuje na hodnotu z originálu bez manuálního kódu. miPress má `origin_id`, ale fallback logiku implementuje ručně na různých místech. Centralizace do traitu tento problém odstraní.

**Lifecycle events v párech**
Statamic má pro každý model dvojici eventů: `EntrySaving` (cancelovatelný — lze vrátit `false` a přerušit uložení) a `EntrySaved` (post-event). miPress používá observery, ale nemá cancelovatelné pre-eventy. Přidání by umožnilo workflow podmínky a validace mimo model.

**Working Copy**
Statamic odděluje `workingCopy()` (rozpracovaná verze, nespuštěná na live) od `revisions()` (archivní historie). miPress ukládá revize při každé změně, ale nemá koncept „pracovní kopie". Toto doplní editorial workflow pro role Editor/Contributor.

**`ContainsComputedData`**
Registrovatelné computed callbacks, které se chovají jako pravá data pole — funguje v šablonách, API, augmentaci shodně s reálnými DB poli. Vhodné pro pole jako `word_count`, `reading_time`, odvozené URL segmenty.

**Blink request cache**
Request-scoped in-memory cache (ne globální `Cache`). Vhodná pro Entry/origin lookups — zabrání N+1 dotazům v jednom requestu bez nebezpečí stale dat mezi requesty.

---

## Definition of Done

- [x] `mipresscz` běží na `packages/mipresscz/core`
- [x] `AdminPanelProvider` v app je tenká dědická vrstva
- [x] Core obsahuje všechny CMS-common součásti
- [x] Installer funguje na čisté instalaci
- [x] Kritické testy pro content + locale + routing + policy jsou zelené (243/243)
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

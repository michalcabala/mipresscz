# miPress CMS — Analýza současného stavu projektu

> Datum: 8. března 2026

---

## 1. Přehled projektu

miPress je modulární CMS postavený na **Laravel 12 + Filament 5**, navržený jako profesionální alternativa WordPressu pro český trh. Cílí na firemní prezentace, weby malých firem a komunitní projekty.

### Technologický stack

| Technologie | Verze | Účel |
|-------------|-------|------|
| PHP | 8.3.4 | Runtime |
| Laravel | 12 | Framework |
| Filament | 5 | Admin panel (SDUI) |
| Livewire | 4 | Reaktivní komponenty |
| Tailwind CSS | 4 | Stylování |
| Pest | 4 | Testování |
| MySQL | 8 | Databáze |
| Laravel Herd | — | Lokální server (`mipresscz.test`) |

### Nainstalované balíčky

| Balíček | Verze | Účel |
|---------|-------|------|
| spatie/laravel-permission | ^7.2 | Role a oprávnění |
| awcodes/mason | ^3.0 | Block-based page builder |
| awcodes/filament-curator | ^5.0 | Správce médií |
| bezhansalleh/filament-language-switch | ^4.1 | Přepínání jazyků (cs/en) |
| caresome/filament-auth-designer | ^3.0 | Design přihlašovacích stránek |
| jeffgreco13/filament-breezy | ^3.1 | Profil uživatele, 2FA |

---

## 2. Architektura obsahu

Obsahový systém je inspirovaný CMS Statamic a používá vzor **Collections → Blueprints → Entries**.

```
Collection (kolekce)
├── Blueprint (šablona polí)
│   └── Entry (záznam/obsah)
│       ├── Revision (revize)
│       └── Term (taxonomický termín) [M:N přes termables]
├── Taxonomy (taxonomie) [M:N přes collection_taxonomy]
│   └── Term (termín)
│       └── children (hierarchie)
└── Entries (záznamy)
    ├── translations (origin_id)
    ├── children (parent_id — strom)
    └── related entries (entry_relationships)
```

### Klíčové vlastnosti

- **ULID primární klíče** na všech content modelech (trait `HasUlids`)
- **Multijazyčnost**: Každý překlad = samostatný Entry se sdíleným `origin_id`
- **Hierarchie**: Entries podporují parent-child vztahy (stromová struktura)
- **Revize**: EntryObserver automaticky vytváří revize při změně, max 50 na záznam
- **Soft deletes**: Na Collections, Blueprints, Entries, Taxonomies, Terms
- **Dynamické blueprinty**: Pole definovaná jako JSON, podpora sekcí a podmínkového zobrazení

---

## 3. Datový model

### Modely (9)

| Model | Tabulka | Hlavní účel |
|-------|---------|-------------|
| `User` | users | Autentizace, role (Spatie) |
| `Collection` | collections | Typ obsahu (stránky, články, reference) |
| `Blueprint` | blueprints | Šablona polí pro kolekci |
| `Entry` | entries | Záznam obsahu (vícejazyčný, hierarchický) |
| `Block` | blocks | Šablona bloku pro page builder |
| `GlobalSet` | global_sets | Globální nastavení (web, sociální sítě, patička) |
| `Revision` | revisions | Historie změn záznamu |
| `Taxonomy` | taxonomies | Klasifikační systém (kategorie, štítky) |
| `Term` | terms | Položka taxonomie |

### Pivotní tabulky

| Tabulka | Vztah |
|---------|-------|
| `collection_taxonomy` | Collection ↔ Taxonomy (M:N) |
| `termables` | Term ↔ Entry (polymorfní M:N) |
| `entry_relationships` | Entry ↔ Entry (M:N s field_handle) |

### Enumy (4)

| Enum | Hodnoty | Použití |
|------|---------|---------|
| `DateBehavior` | None, Required, Optional | Chování data v kolekci |
| `DefaultStatus` | Draft, Published | Výchozí stav nových záznamů |
| `EntryStatus` | Draft, Published, Scheduled, Archived | Stav záznamu (s ikonou a barvou) |
| `UserRole` | SuperAdmin, Admin, Editor, Contributor | Uživatelské role |

---

## 4. Současný stav dat

### Kolekce

| Kolekce | Handle | Blueprinty | Záznamy | Ikona | Stav |
|---------|--------|------------|---------|-------|------|
| Stránky | `pages` | 2 (standard, landing) | 3 | `fal-file-lines` | Aktivní |
| Články | `articles` | 1 (article) | 6 | `fal-newspaper` | Aktivní |
| Reference | `testimonials` | 1 (testimonial) | 3 | `fal-quote-right` | Aktivní |

### Taxonomie

| Taxonomie | Handle | Termíny | Hierarchická |
|-----------|--------|---------|--------------|
| Kategorie | `categories` | 7 (Tech→PHP, Laravel, JS; Design→UI, UX) | Ano |
| Štítky | `tags` | 4 (Tutorial, Novinka, Tip, Recenze) | Ne |

### Globální sady

| Sada | Handle | Klíčová data |
|------|--------|-------------|
| Nastavení webu | `site` | Název, popis, kontakt, logo, favicon |
| Sociální sítě | `social` | Facebook, Instagram, X, YouTube, LinkedIn |
| Patička | `footer` | Copyright, zobrazit sociální, extra HTML |

### Bloky (9 typů)

Hero, Text, Text s obrázkem, Galerie, Video, Citát, Výzva k akci, Akordeon, Karty

### Souhrn

| Entita | Počet |
|--------|-------|
| Uživatelé | 2 |
| Záznamy (entries) | 12 |
| Revize | 13 |
| Bloky (šablony) | 9 |
| Globální sady | 3 |

---

## 5. Admin panel (Filament)

### Přístup

- **URL**: `mipresscz.test/mpcp`
- **Panel ID**: `admin`
- **Pluginy**: AuthDesigner (login design), BreezyCore (profil, 2FA)

### Registrované zdroje (Resources)

| Resource | URL | Navigační skupina | Typ |
|----------|-----|-------------------|-----|
| Dashboard | `/mpcp` | — | Výchozí stránka |
| **Stránky** | `/mpcp/pages` | Obsah | Dynamický (EntryResource) |
| **Články** | `/mpcp/articles` | Obsah | Dynamický (EntryResource) |
| **Reference** | `/mpcp/testimonials` | Obsah | Dynamický (EntryResource) |
| Kolekce | `/mpcp/collections` | Obsah | Statický |
| Taxonomie | `/mpcp/taxonomies` | Obsah | Statický |
| Bloky | `/mpcp/blocks` | Obsah | Statický |
| Globální nastavení | `/mpcp/globals/global-sets` | Obsah | Statický |
| Uživatelé | `/mpcp/users` | — | Statický |
| Můj profil | `/mpcp/my-profile` | — | BreezyCore |

### Dynamická navigace (Configurable Resources)

Systém využívá Filament 5 **ResourceConfiguration** pattern:

1. `AdminPanelProvider::getCollectionResources()` načte aktivní kolekce z DB
2. Pro každou vytvoří `EntryResource::make(handle)` s vlastním slugem, labelem a ikonou
3. `EntryResourceConfiguration` nese `collectionHandle`, `navigationLabel`, `navigationIcon`, `navigationSort`
4. `EntryResource::shouldRegisterNavigation()` vrací `hasConfiguration()` — v navigaci se zobrazí jen konfigurované varianty
5. Base route `/mpcp/entries` existuje, ale je skrytá z navigace

### Registrované routy (33 GET)

```
mpcp/                           → Dashboard
mpcp/pages[/create|/{id}/edit]  → Stránky (dynamický EntryResource)
mpcp/articles[/...]             → Články (dynamický EntryResource)
mpcp/testimonials[/...]         → Reference (dynamický EntryResource)
mpcp/entries[/...]              → Záznamy — base (skrytý z nav)
mpcp/collections[/...]          → Kolekce
mpcp/taxonomies[/...]           → Taxonomie
mpcp/blocks[/...]               → Bloky
mpcp/globals/global-sets[/...]  → Globální nastavení
mpcp/users[/...]                → Uživatelé
mpcp/my-profile                 → Profil
mpcp/login                      → Přihlášení
mpcp/password-reset/...         → Reset hesla
mpcp/two-factor-authentication  → 2FA
```

---

## 6. Frontend

### Routování

```php
// routes/web.php
Route::get('/', fn () => view('welcome'));
Route::get('{uri}', [EntryController::class, 'show'])->where('uri', '.*');
```

- Catch-all route na konci — řeší CMS záznamy dle URI
- `EntryController::show()` hledá publikované záznamy dle `uri` sloupce
- Fallback šablona: `resources/views/entries/show.blade.php`

### Stav frontendu

- **Minimalistický** — pouze `welcome.blade.php` a `entries/show.blade.php`
- Žádné layout, žádné komponenty, žádné menu
- CSS/JS: standardní `app.css` + `app.js` + Vite build
- **Frontend je zatím nerozpracovaný** — potřebuje kompletní implementaci

---

## 7. Testování

### Konfigurace

- Framework: **Pest 4**
- Databáze: MySQL (`mipresscz_testing`)
- Base: `tests/Pest.php` → TestCase + RefreshDatabase pro Feature testy

### Aktuální testy

| Soubor | Počet testů | Pokrytí |
|--------|-------------|---------|
| `ContentSystemTest.php` | 30 | Modely, vztahy, scopes, enumy, GlobalSet, revize |
| `EntryRoutingTest.php` | 4 | Frontend routing, 404, draft, nested URI |
| `ExampleTest.php` (Feature) | 1 | HTTP response |
| `ExampleTest.php` (Unit) | 1 | Základní assertion |
| **Celkem** | **36 testů, 65 assertions** | **Vše prochází** ✅ |

### Nepokryté oblasti

- Filament resource testy (Livewire::test)
- Autorizace a role
- Uživatelský resource
- Block a GlobalSet resources
- Validace formulářů
- Edge cases multijazyčnosti

---

## 8. Migrace (19)

### Systémové (3)

| Migrace | Tabulky |
|---------|---------|
| `create_users_table` | users, password_reset_tokens, sessions |
| `create_cache_table` | cache, cache_locks |
| `create_jobs_table` | jobs, job_batches, failed_jobs |

### Vlastní (16)

| Migrace | Tabulka | Poznámka |
|---------|---------|----------|
| `create_breezy_sessions_table` | breezy_sessions | 2FA sessions |
| `alter_breezy_sessions_table` | breezy_sessions | Drops unused cols |
| `add_role_to_users_table` | users | Adds role column |
| `create_permission_tables` | roles, permissions, pivoty | Spatie Permission |
| `create_collections_table` | collections | ULID, SoftDeletes |
| `create_taxonomies_table` | taxonomies | ULID, SoftDeletes |
| `create_blueprints_table` | blueprints | FK → collections |
| `create_entries_table` | entries | Complex: FKs, unique constraints, indexes |
| `create_terms_table` | terms | Hierarchical, FK → taxonomies |
| `create_revisions_table` | revisions | No auto timestamps |
| `create_blocks_table` | blocks | ULID, no SoftDeletes |
| `create_global_sets_table` | global_sets | Multi-locale |
| `create_collection_taxonomy_table` | collection_taxonomy | Pivot |
| `create_termables_table` | termables | Polymorphic pivot |
| `create_entry_relationships_table` | entry_relationships | Entry ↔ Entry |

---

## 9. Ikony

- **Font Awesome Light** (`fal-*`): 1000+ SVG ikon v `resources/svg/fa/light/`
- **Font Awesome Brands** (`fab-*`): 500+ SVG ikon v `resources/svg/fa/brands/`
- **Heroicon**: Použití přes `Filament\Support\Icons\Heroicon` enum
- **IconServiceProvider**: Aliasy pro Filament ikony (dashboard = `fal-gauge-high`)

---

## 10. Git

### Historie commitů

```
5dc0fc0 (HEAD → main)      fix: invert shouldRegisterNavigation logic
373e72f (origin/main)       feat: add rich editor integration and mason components
5badc7e                     feat: lokalizované popisky pro uživatele
7765d05                     first commit
```

### Nepushnuté změny

1 commit ahead of `origin/main` — fix navigační logiky pro dynamické entry resources.

---

## 11. Struktura souborů

```
app/
├── Enums/              (4 enumy)
├── Filament/
│   └── Resources/
│       ├── Blocks/     (Resource + Pages/ + Schemas/ + Tables/)
│       ├── Collections/(Resource + Pages/ + Schemas/ + Tables/)
│       ├── Entries/    (Resource + Configuration + Pages/ + Schemas/ + Tables/)
│       ├── Globals/    (Resource + Pages/ + Schemas/ + Tables/)
│       ├── Taxonomies/ (Resource + Pages/ + Schemas/ + Tables/)
│       └── Users/      (Resource + Pages/ + Schemas/ + Tables/)
├── Http/Controllers/   (Controller, EntryController)
├── Models/             (9 modelů)
├── Observers/          (EntryObserver)
├── Providers/
│   ├── AppServiceProvider.php
│   ├── IconServiceProvider.php
│   └── Filament/AdminPanelProvider.php
└── helpers.php

database/
├── factories/          (6 factories)
├── migrations/         (19 migrací)
└── seeders/            (5 seederů)

lang/
├── cs/                 (content.php, roles.php, users.php)
└── en/                 (content.php, roles.php, users.php)

resources/
├── css/                (app.css, filament/admin/theme.css)
├── js/                 (app.js, bootstrap.js)
├── svg/fa/             (light/ + brands/ — FontAwesome SVGs)
└── views/              (welcome.blade.php, entries/show.blade.php)

tests/
├── Pest.php
├── TestCase.php
├── Feature/            (ContentSystemTest, EntryRoutingTest, ExampleTest)
└── Unit/               (ExampleTest)
```

---

## 12. Co chybí / Další kroky

### Vysoká priorita

1. **Frontend šablony** — Layout, navigace, stránky, články, homepage
2. **Mason integrace** — Block-based obsah v Entries (propojení s Blueprinty)
3. **Curator integrace** — Media picker v Entry formulářích
4. **Oprávnění na resources** — Policy classes pro Collections, Entries, atd.

### Střední priorita

5. **Filament testy** — Livewire::test pro všechny resources
6. **SEO pole** — Meta title, description, OG image na Entries
7. **Navigace (frontend)** — Menu builder nebo automatické menu z kolekcí
8. **Vyhledávání** — Full-text search přes záznamy
9. **Slug unikátnost** — Across collections (aktuálně unique per collection+locale)

### Nízká priorita

10. **API** — REST/JSON API pro headless přístup
11. **Import/Export** — Content migration nástroje
12. **Koše** — UI pro obnovení soft-deleted záznamů
13. **Activity log** — Audit trail pro admin akce
14. **Cache strategie** — Invalidation při změnách obsahu

---

*Vygenerováno: 8. března 2026*

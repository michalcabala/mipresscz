# miPress CMS - Project Analysis

Datum: 17. března 2026

## Účel dokumentu

Tento dokument popisuje aktuální stav projektu `mipresscz` po kompletním odstranění původního revisions / working copy workflow. Vychází z reálného kódu, rout, Filament administrace, seedů, Mason bricků a z ověřených testů.

## Executive Summary

miPress je dnes robustní Laravel CMS s jasně odděleným core balíčkem v `packages/mipresscz/core` a tenkou aplikační vrstvou v `app/`. Jádro už pokrývá většinu funkcí potřebných pro první produkční obsahový web: obsahový model, administraci, lokalizaci, SEO, menu builder, média, Mason bloky, template systém, preview, feed, sitemapu a role/oprávnění.

Zásadní změna proti předchozímu stavu je odstranění revisions subsystemu. Entry workflow je teď jednodušší: publikovaný obsah se upravuje přímo, bez working copy, diffů a timeline workspace. Tím zmizel hlavní zdroj nestability, který dříve blokoval `EntryResourceTest`.

## Ověřený Snapshot

### Stack a runtime

- PHP 8.3.x
- Laravel 12
- Filament 5
- Livewire 4
- Tailwind CSS 4
- Laravel Scout
- Spatie Feed
- Spatie Permission
- Filament Curator
- Mason

### Architektura

- Core CMS logika je v `packages/mipresscz/core`.
- Root aplikace řeší hlavně user management, Mason brick registry, admin rozšíření a app-specific vrstvy.
- Frontend catch-all routy registruje core provider až po app routách, takže app vrstva může core chování přebít.
- Admin běží na `/mpcp`.
- Aplikace má health endpoint `/up`.
- Login rate limit je definovaný v `bootstrap/app.php`.

### Test baseline k 17. 3. 2026

- Poslední plný běh suite po odstranění revisions:
  - `556` testů prošlo,
  - `1242` assertions,
  - bez pádů.
- Potom byl odstraněn ještě warning v `tests/Feature/TermResourceTest.php` a tento soubor znovu prošel samostatně:
  - `3` testy prošly,
  - `16` assertions.
- Cílený regression průchod nad hlavními dotčenými feature testy po odstranění revisions:
  - `111` testů prošlo,
  - `282` assertions.

Závěr: po odstranění revisions je suite opět zelená a dřívější blocker v entries workflow už neexistuje.

## Feature Inventory

Níže je inventář aktuálně implementovaných funkcí. Jde o funkce ověřené v kódu, ne o wishlist.

### 1. Obsahové jádro CMS

- Collections jako typy obsahu.
- Blueprints jako definice polí pro jednotlivé kolekce.
- Entries jako hlavní obsahové záznamy.
- Taxonomies a Terms pro klasifikaci obsahu.
- Global Sets pro globální datové sady.
- Locales jako databázově řízená jazyková konfigurace.
- ULID identifikátory na content modelech.
- Soft deletes na hlavních obsahových modelech.
- Enumy pro `EntryStatus`, `DefaultStatus`, `DateBehavior`.
- JSON-driven blueprint fields.
- Překladové vazby přes `origin_id`.
- Translations a sibling varianty pro entries, terms a global sets.
- Scope a helper vrstva pro draft/published/root/collection/locale dotazy.
- Podpora parent-child hierarchie u tree collections.
- Podpora related entries přes pivot `entry_relationships`.

### 2. Collections a routing pravidla

- Collection umí definovat:
  - aktivitu,
  - ikonku,
  - tree režim,
  - route template,
  - výchozí status,
  - datumové chování,
  - výchozí řazení.
- Route template podporuje placeholdery:
  - `{slug}`
  - `{year}`
  - `{month}`
  - `{day}`
- Default seeder vytváří minimálně kolekci `pages`.
- Demo content seeder vytváří i `articles` a `testimonials`.

### 3. Blueprint a field systém

- Blueprint resource v adminu.
- Výchozí blueprint per collection.
- Dělení polí minimálně na `main` a `sidebar`.
- Typed field rendering podle blueprint definice.
- Podporované field typy ve formuláři entries:
  - `text`
  - `textarea`
  - `rich_editor`
  - `mason`
  - `curator` / `media`
  - `select`
  - `toggle`
  - `number`
  - `datetime`
  - `date`
  - `time`
  - `color`
  - `tags`
  - `url`
  - `email`
  - `markdown`
  - `checkbox`
  - `radio`
  - `checkbox_list`
  - `entries`
- Blueprints umí `use_mason`.
- Když je `use_mason` aktivní, obsah se edituje přes Mason editor.
- App command `mipress:migrate-blueprint-fields` migruje starý typ `media` na `curator`.

### 4. Entry management a editorial workflow

- Seznam entries v Filamentu.
- Dynamické resource varianty podle aktivních kolekcí.
- Scoped collection navigation v adminu.
- Create/edit/delete entries.
- Save as draft.
- Publish entry.
- Unpublish entry.
- Přímá editace publikovaného obsahu bez mezivrstvy working copy.
- SEO subpage pro entry.
- Featured image přes Curator.
- Author assignment.
- Publikační datum.
- Homepage flag.
- Pinning entries.
- Parent selection u tree kolekcí.
- Slug auto-generation.
- Slug uniqueness v rámci collection + locale.
- URI regeneration při změně title/slug.
- Computed data v entry detailu:
  - `word_count`
  - `reading_time`
- Preview token s expirací.
- Preview URL pro neveřejnou verzi entry.

### 5. Taxonomie a termy

- Taxonomy resource.
- Term resource.
- Dynamické term resources podle aktivních taxonomií.
- Navigace taxonomií pod přiřazenou kolekcí.
- Hierarchické taxonomie.
- Nehierarchické taxonomie.
- Parent-child-grandchild struktura termů.
- Scoped taxonomy filters v entry tabulkách.
- Hierarchical select tree filtr pro nested terms.
- Locale-aware terms.
- Překladové vazby u termů přes `origin_id`.
- Slug uniqueness per taxonomy + locale.

### 6. Global sets a site settings

- GlobalSet resource.
- Caching globálních setů podle handle a locale.
- Fallback na origin/default variantu global setu.
- Helper `GlobalSet::getValue()`.
- Site settings page.
- Výběr homepage entry z adminu.
- Ruční flush aplikace cache z adminu.

### 7. Lokalizace a jazykové workflow

- Databázově řízené locales.
- Admin page pro správu locales.
- Reorder locales.
- Nastavení default locale.
- Aktivace/deaktivace locale.
- Samostatné povolení locale pro admin.
- Samostatné povolení locale pro frontend.
- `url_prefix` per locale.
- `fallback_locale`.
- `direction` a `date_format`.
- Locale observer pro cache invalidaci a jedno default locale.
- Locale service jako centrální vrstva nad lokalizací.
- Rozlišení single-language a multi-language režimu.
- Locale-prefixed URL při více frontend locales.
- Neprefixované URL při jednom frontend locale.
- Přesměrování z prefixované URL v single-language režimu.
- Admin language switch přes plugin `filament-language-switch`.
- Frontend language switcher komponenta s linkováním na překlad entry.

### 8. Frontend routing a rendering

- Catch-all frontend routing přes entries.
- Locale-prefixed i neprefixované routy.
- Homepage fallback přes `is_homepage`.
- Archive rendering odvozené z collection `route_template`.
- Přepínání archive view mode přes query `view=grid|list`.
- View resolution s prioritou:
  - aktivní template namespace,
  - app-level fallback,
  - package fallback.
- Podpora detailu stránky, detailu článku, homepage a archivu.
- Custom error šablony:
  - 404
  - 500
  - 503

### 9. Frontend template systém

- TemplateManager služba.
- Skenování `resources/views/templates/*/template.json`.
- Aktivní template uložená v settings.
- Blade namespace `template::`.
- Fallback na default theme při chybějících view v aktivní šabloně.
- Filament page pro aktivaci template.
- CLI příkaz `mipresscz:template:list`.
- Aktuálně přítomná default template vrstva:
  - layout
  - header
  - footer
  - nav
  - pages/home
  - pages/page
  - pages/archive
  - articles/show
  - errors/*

### 10. Mason a blokový obsah

- Mason integrace v entry formuláři.
- Mason rendering na frontendu.
- App registry `BrickCollection`.
- Aktuálně registrovaných 17 bricků:
  - Hero
  - Features
  - Stats
  - Cta
  - Cards
  - LatestEntries
  - Text
  - Heading
  - Image
  - Gallery
  - Quote
  - Testimonial
  - Columns
  - Video
  - Button
  - Divider
  - Html
- App command `mipress:migrate-content-to-mason` pro migraci starého HTML obsahu do Mason struktury.
- Plain text extraction z Mason obsahu pro computed fields a search.

### 11. Media management

- Filament Curator plugin.
- Featured image pro entries.
- Meta OG image pro entries.
- Media folders resource.
- Tree page pro media folders.
- Media tags resource.
- Custom media form/table vrstva.
- Vztah media-folder a media-tag k Curator médiím.
- Testované tagování médií.

### 12. Navigace a menu builder

- Vlastní MenuManager plugin/page v Filamentu.
- Registrované menu lokace:
  - `primary`
  - `footer`
- Více menu per location.
- Aktivní menu per location.
- Livewire menu builder.
- Livewire panel pro přidávání položek.
- Typy menu položek:
  - custom link
  - model link
  - archive link
- Zdroje modelových odkazů jsou registrovatelné z app vrstvy.
- Archive položky se generují z aktivních collections s archive URL.
- Drag and drop řazení položek.
- Button-based reorder:
  - move up
  - move down
  - indent
  - outdent
- Edit title / URL / target / enabled stavu položky.
- Rekurzivní mazání položek.
- Frontend tree rendering jen pro aktivní a enabled položky.
- Caching navigace na frontendu.

### 13. SEO, discoverability a syndikace

- SEO pole na entries:
  - `meta_title`
  - `meta_description`
  - `meta_og_image_id`
- Canonical URL generování.
- Hreflang linky pro překlady.
- Feedable entries přes Spatie Feed.
- `feed.xml` pro default locale.
- `/{locale}/feed.xml` pro prefixované locale.
- `sitemap.xml` endpoint servírovaný ze statického souboru v public.
- Filament sitemap generator plugin.
- Filament stránky pro sitemap settings a sitemap generation.
- Scheduler pro denní generování sitemap ve 02:00.
- Botly plugin pro správu `robots.txt`.
- Meta robots tag v default layoutu.
- Search index nad title/slug/locale/meta poli.
- Search stránka s minimální délkou dotazu 2 znaky.
- Search vrací pouze publikovaný obsah v aktuálním locale.

### 14. Výkon a cache

- Full-page cache middleware pro frontend entries.
- Cache pouze pro cacheovatelné GET requesty.
- Bez cache pro:
  - authenticated requesty,
  - query string requesty,
  - neúspěšné odpovědi.
- Deterministické page cache keys podle URI + locale.
- Samostatná nav cache.
- Index cache keys pro hromadný flush.
- Cache invalidation subscriber pro entry save/delete.
- Flush homepage cache při změně homepage entry.
- Flush nav cache při změně obsahu.
- Flush locale cache při změně locales.

### 15. Dashboard a admin UX

- Filament dashboard.
- Widget `EntryStatsWidget`.
- Widget `LatestEntriesWidget`.
- SPA režim adminu.
- Unsaved changes alerts.
- DB transactions na panelu.
- Collapsible sidebar.
- „View site“ action z adminu.
- Slideover column manager.
- Slideover filters.
- Reorderable columns.
- Deferred table loading.
- Stacked-on-mobile tables.
- Filament Auth Designer pro login screen.

### 16. Uživatelé, role a oprávnění

- User resource v adminu.
- Soft delete users.
- Restore users.
- Force delete users.
- Ochrana proti smazání vlastního účtu.
- Role enum:
  - SuperAdmin
  - Admin
  - Editor
  - Contributor
- Sync role column do Spatie rolí.
- Gate::before bypass pro SuperAdmin.
- Jemná permission matice pro:
  - users
  - collections
  - entries
  - taxonomies
  - global sets
  - menus
  - media
  - locales
  - settings

### 17. Bezpečnost a provozní minimum

- Security headers middleware:
  - `X-Content-Type-Options`
  - `X-Frame-Options`
  - `Referrer-Policy`
  - `Permissions-Policy`
  - `X-XSS-Protection`
  - `Strict-Transport-Security` mimo local
- Login rate limiting.
- Health endpoint `/up`.
- Password reset v adminu.

### 18. Instalace, seedování a interní tooling

- `php artisan mipresscz:install`
- Installer umí:
  - migrace,
  - role a permissions,
  - locales,
  - default collections,
  - admin user,
  - volitelně demo content.
- Seedery pro:
  - locales
  - globals
  - default collections
  - content
- Demo content obsahuje:
  - homepage
  - statické stránky
  - články
  - reference
  - categories/tags
  - anglický překlad článku

## Silné Stránky

- Dobře oddělené core vs. app.
- Široký CMS feature set na relativně kompaktním kódu.
- Databázově řízená lokalizace místo hardcoded configu.
- Stabilnější editorial workflow po odstranění revisions.
- Silná Filament administrace s dynamickými resources.
- Template systém, který už není jen demo placeholder.
- Rozumné SEO a discoverability minimum už v jádru.
- Široké automatické testování napříč modelem, adminem i frontendem.

## Slabá Místa a Rizika

### 1. Release a dokumentační drift

- `packages/mipresscz/core/composer.json` je stále na `0.6.0`.
- `CHANGELOG.md` a část dokumentace bylo potřeba srovnat s realitou po odstranění revisions.
- Je potřeba držet jednotný release contract mezi kódem, changelogem a docs.

### 2. Frontend maturity

- Default template je použitelná, ale pořád je to spíš produkční baseline než definitivně odladěná theme.
- Chybí formální accessibility pass a browser QA.

### 3. Browser/E2E vrstva

- Pest coverage je silná.
- Chybí browser smoke vrstva pro klíčové admin user journeys.

### 4. Ops a deployment disciplína

- Projekt má technické předpoklady pro deploy, ale chybí explicitní provozní runbook.
- Scheduler, cache warmup, rollback a monitoring nejsou formalizované.

## Co V Projektu Naopak Chybí

Tyto oblasti jsem v aktuálním kódu nenašel jako hotovou feature:

- veřejná headless API vrstva,
- browser E2E testy,
- formalizovaný deploy/rollback runbook,
- více produkčně odladěných frontend themes,
- observability vrstva nad rámec standardních logů,
- revisions / content history subsystem.

## Produkční Připravenost

### Připravené nebo téměř připravené oblasti

- obsahový model,
- admin CRUD pro hlavní entity,
- lokalizace,
- SEO minimum,
- menu a média,
- template přepínání,
- install flow,
- role a oprávnění.

### Oblasti, které ještě brání čistému release

- release alignment po odstranění revisions,
- produkční runbook,
- frontend QA,
- redakční UAT přímého publikačního workflow,
- browser smoke coverage.

## Hodnocení

| Oblast | Stav |
| --- | --- |
| Architektura | 9/10 |
| CMS jádro | 8.8/10 |
| Filament admin | 8.7/10 |
| Lokalizace | 9/10 |
| SEO a routing | 8.5/10 |
| Frontend template baseline | 7/10 |
| Test coverage | 9/10 |
| Release governance | 6/10 |
| Produkční připravenost | 8.1/10 |

## Závěr

miPress je dnes robustní CMS základ s širokým rozsahem hotových funkcí a se znovu zelenou test suite. Odstranění revisions zjednodušilo editorial workflow a současně odstranilo hlavní zdroj nestability v entries administraci.

Co dnes nejvíc brání produkční verzi, už není rozbitá feature v jádru. Jsou to hlavně release disciplina, provozní runbook, frontend QA a potvrzení redakčních workflow v praxi.

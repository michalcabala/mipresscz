# miPress CMS — Project Analysis

Datum: 15. března 2026

## Účel dokumentu

Tento dokument je hlavní sjednocená analýza projektu miPress. Nahrazuje předchozí samostatné souhrny a audity. Slouží jako zdroj pro onboarding, technické rozhodování, plánování roadmapy a průběžné vyhodnocování rizik.

## Executive Summary

miPress je vlastní CMS nad Laravel 12 a Filament 5. Projekt už má jasně definované jádro: databázově řízený obsahový model, dynamické Filament resources, locale systém řízený z databáze, admin integraci pro média a block builder a solidní základ testů nad MySQL.

Nejsilnější stránky:

- čistý obsahový model inspirovaný Statamicem (HasOrigin fallback, computed data, lifecycle events),
- rozumně navržený Filament admin s dynamic resource patternem,
- locale workflow řízený z databáze,
- kompletní SEO (sitemap, feed, hreflang, canonical, meta tagy),
- fulltext vyhledávání, menu builder, entry preview, caching strategie,
- solidní test suite (497 testů) pokrývající backend i admin UX.

Největší průřezová rizika:

- locale a URL logika zasahuje několik vrstev současně,
- změny v collections mají nepřímý dopad do admin navigace,
- frontend je zatím pouze základ (fallback šablona),
- dokumentace je potřeba aktivně udržovat, jinak rychle vzniká drift.

## Stack a tooling

### Backend

- PHP 8.3.x runtime
- Laravel 12
- Filament 5
- Livewire 4
- MySQL 8

### Hlavní balíčky

- `spatie/laravel-permission`
- `awcodes/mason`
- `awcodes/filament-curator`
- `bezhansalleh/filament-language-switch`
- `jeffgreco13/filament-breezy`
- `caresome/filament-auth-designer`
- `openplain/filament-tree-view`
- `codewithdennis/filament-select-tree`
- `laravel/scout` (database driver)

### Frontend tooling

- Vite 7
- Tailwind CSS 4
- Axios
- `flagpack-core`

### Praktická poznámka

- `composer.json` stále obsahuje default Laravel metadata, proto je při orientaci spolehlivější číst kód a dokumentaci v `docs/` než package metadata.

## Architektura systému

### Obsahový model

Projekt používá CMS vzor:

- Collections
- Blueprints
- Entries
- Taxonomies a Terms
- Global Sets
- Revisions
- Locales

Klíčové technické rysy:

- ULID primární klíče na content modelech,
- JSON field definice v blueprintech,
- entry překlady řešené přes `origin_id`,
- soft deletes na hlavních content modelech,
- enum-based stavy a konfigurace.

### Hlavní modely a odpovědnosti

Všechny content modely žijí výhradně v `packages/mipresscz/core/src/Models/`. `app/Models/` obsahuje pouze `User.php`.

- `Collection`: typ obsahu, routing pravidla, řazení, stav a vazby na blueprinty a taxonomie.
- `Blueprint`: struktura polí a rozdělení do sekcí, včetně translatable/non-translatable logiky.
- `Entry`: hlavní obsahový záznam s URI, locale, publikací, překladem, hierarchií, termy a related entries.
- `GlobalSet`: globální obsah s locale fallbackem a cache-on-read přístupem.
- `Locale`: databázový zdroj pravdy pro jazykové chování frontendu i adminu.
- `Revision`: historizace změn entry.

### Modulární síla a technický tlak

Obsahová doména je dobře navržená, ale `Entry` už teď nese velkou část důležité logiky. Složitější pravidla jsou postupně přesouvat do služeb:

- `LocaleService` — centrální zdroj pravdy pro locale rozhodování
- `CacheService` — singleton se smart invalidací pro entry, navigation a page cache
- `ComputedFieldRegistry` — singleton registry pro vypočítaná pole (wildcard + scoped)
- `NavComposer` — view composer pro cachované navigační menu

### Traity a concerns

- `HasOrigin` — centralizovaný i18n fallback pro Entry, Term, GlobalSet (inspirace Statamic)
- `ContainsComputedData` — virtuální/vypočítaná pole registrovaná přes ComputedFieldRegistry
- `HasLocalizedTitle` — multijazykové titulky pro Collection + Taxonomy
- `HasMenuItems` — polymorfní vazba na menu builder

### Middleware

- `SetFrontendLocale` — frontend locale z URL prefixu
- `SecurityHeaders` — CSP, X-Frame-Options, Referrer-Policy, Permissions-Policy, HSTS
- `PageCacheMiddleware` — full-page cache pro entry routes s auto-invalidací

### Events

- `EntrySaving` / `EntrySaved` — cancelovatelné lifecycle event páry (Statamic-inspired)
- `EntryDeleted` — trigger pro cache invalidaci

## Filament admin architektura

Admin panel běží na `/mpcp` a je definovaný v `app/Providers/Filament/AdminPanelProvider.php`.

### Ověřené architektonické vzory

- Filament resources drží split pattern `Resource.php` + `Schemas/*` + `Tables/*` + `Pages/*`.
- `AppServiceProvider` globálně nastavuje baseline chování Filament tabulek (`striped`, `deferLoading`, `stackedOnMobile`).
- Panel customizace jsou soustředěné v provideru, včetně render hooků.
- Locale správa není resource, ale samostatná Filament page `ManageLocales`.

### Dynamic Entry Resources

Nejdůležitější repo-specific pattern je dynamická konfigurace entry resources podle aktivních kolekcí:

- `AdminPanelProvider::getCollectionResources()` načítá aktivní kolekce,
- `EntryResourceConfiguration` přenáší konfigurační data pro navigaci a slug,
- jedna entry resource logika tak obsluhuje více kolekčně specifických admin sekcí.

To přináší výhodu vysoké flexibility, ale také riziko, že změna v collections nebo konfiguraci resource nepřímo rozbije admin navigaci, routy nebo očekávané chování tabulek a formulářů.

### Admin rizika a doporučení

Rizika:

- rozšiřování topbaru a panel customizací do více míst,
- netriviální dopad změn v `Collection` na admin navigaci,
- nižší coverage složitějších Filament interakcí.

Doporučení:

1. Držet panel customizace co nejvíc centralizované v `AdminPanelProvider`.
2. Zachovat jeden jednoznačný pattern pro dynamické entry resources.
3. Postupně doplnit Livewire/Filament testy pro důležité admin workflow.

## Lokalizace a URL logika

Locale workflow je jedna z nejdůležitějších a nejcitlivějších částí systému.

### Hlavní stavební kameny

- `Locale`
- `LocaleService`
- `SetFrontendLocale`
- `ManageLocales`
- `LanguageSwitcher`
- locale-aware URL generování v `Entry`

### Aktuální pravidla

- admin i frontend dostupnost locale se řídí samostatnými flagy,
- frontend používá prefixované i neprefixované route varianty,
- pokud existuje více frontend jazyků, používají se locale prefixy,
- pokud je aktivní jen jeden frontend jazyk, prefix se nepoužívá,
- prefixované URL jsou v single-language režimu přesměrovány na čistou variantu,
- hreflang a URL generování je odvozené od locale služby a entry vztahů.

### Rizika locale subsystému

- změna jedné části často vyžaduje změnu ve službě, middleware, URL generování, blade komponentě i testech,
- prefix/no-prefix režimy zvyšují kombinatoriku testovacích scénářů,
- UI změna v locale správě může nepřímo rozhodit frontend přepínání a canonical URL chování.

### Doporučení

1. Každou locale změnu ověřovat minimálně na úrovni služby, modelu a HTTP chování.
2. Dál držet `LocaleService` jako centrální zdroj pravdy pro locale rozhodování.
3. Pokud URL logika dál poroste, zvážit vyčlenění specializované služby pro frontend URL resolution.

## Frontend a asset pipeline

Frontend je zatím poměrně lehký a opírá se o blade views a CMS routing.

### Ověřený stav

- `routes/web.php` používá root route a locale-aware catch-all entry routing,
- `EntryController` rozhoduje o renderingu entry view,
- `resources/views/` už obsahuje `components`, `entries`, `filament`, `mason`, `vendor` a `welcome.blade.php`,
- Vite build používá tři vstupy: app CSS, app JS a admin theme CSS,
- `resources/css/filament/admin/theme.css` už integruje Filament, Mason i Curator a drží potřebné `@source` cesty.

### Rizika

- frontend je zatím spíš základ než plně rozvinutá prezentační vrstva,
- při rychlém rozšiřování mohou vzniknout nekonzistentní layouty a duplicita komponent,
- `welcome.blade.php` a fallback views mohou časem míchat demo a skutečný produkční obsah.

### Doporučení

1. Při růstu frontendu zavést jasný layout a komponentový pattern.
2. Standardizovat práci s global sets a locale switcherem do několika sdílených komponent.
3. Udržet admin styling na existujícím theme entrypointu a neobcházet ho novými build cestami.

## Content builder a média

Projekt už má integrované dvě klíčové stavební vrstvy pro obsah:

- Mason pro block builder obsah,
- Curator pro média.

### Co funguje dobře

- admin theme je už připravená pro oba pluginy,
- seedery ukazují realistický Mason obsah,
- entries mají připravené media vztahy přes Curator model.

### Rizika a mezery

- Mason brick rendering má HTML výstupní testy, ale chybí snapshotové porovnání.
- Curator media workflow pokrytý testy (featured image, galerie), ale ne upload flow.
- Frontend blade views nemají automatizované testy.

---

## Testové pokrytí

### Aktuální stav (10. března 2026)

**185 testů, 371 assertions — všechny procházejí.**

### Co je dobře pokryto

- Content model relationships (Collection ↔ Blueprint ↔ Entry)
- Entry routing scénáře (locale prefix, single/multi-language, redirecty)
- Policy autorizace (všechny role vs hlavní resources)
- LocaleService + observers + cache invalidace
- Factory generování + seeding
- Installer command reprodukovatelnost
- Permissions seeding + user role sync

### Mezery v pokrytí

| Oblast | Počet testů | Riziko |
|--------|-------------|--------|
| Filament form CRUD (create, edit, validate) | 0 | Vysoké — admin UX může potichu zhavarovat |
| Filament table interakce (filtry, řazení, bulk) | 0 | Střední — regresní chyby neodhaleny |
| Dynamic resource creation (`getCollectionResources`) | 0 | Střední — klíčová admin logika |
| Mason brick HTML rendering | 0 | Nízké — vizuální regrese |
| Curator media workflow | 0 | Nízké — upload/display flow |
| Livewire komponenty (LanguageSwitcher) | 0 | Nízké — interakční chyby |

---

## Technický dluh

### Kritický

1. **TermPolicy chybí** — 5 z 6 content modelů má policy, Term ne. Riziko AuthorizationException při Filament operacích s termy.
2. **Admin UX nulově otestovaný** — žádné Filament form/table/interaction testy. Změny v UI mohou potichu rozbít funkčnost.
3. **Branch `refactor/core-extraction` nemergnuta** — 18 commitů čeká na merge do `main`.

### Střední

4. **Dva language switcher balíčky** — `bezhansalleh/filament-language-switch` + `craft-forge/filament-language-switcher` instalovány současně. Nejasné, který je aktivní.
5. **Breezy překlady chybí** — 2FA UI strings nepřeloženy do češtiny.
6. **README.md je default Laravel placeholder** — neodpovídá realitě projektu.
7. **Blocks tabulka v DB deprecated** — migrace ji stále vytváří, ale Mason je náhradou.

### Nízký

8. **Entry model mohutní** — ~90+ metod; URL/locale logika by mohla být extrahována do service.
9. **Policies mají duplicitní logiku** — DRY potenciál napříč rolemi.
10. **`public/build/` může být stale** — Vite manifest nemusí odpovídat aktuálním assets.

---

## Matice funkční kompletnosti

| Funkce | Stav | Poznámka |
|--------|------|----------|
| Obsahový model (Collections → Entries) | ✅ Kompletní | Flexibilní, vícejazyčný, revize, HasOrigin fallback |
| Admin panel (Filament) | ✅ Kompletní | 6 resource skupin, dynamické entry resources |
| Role & oprávnění | ✅ Kompletní | 4 role, 14 permissions, 6 policies (vč. TermPolicy) |
| Locale systém | ✅ Kompletní | Multi/single-language routing, DB-driven |
| Block builder (Mason) | ✅ Kompletní | 13+ bricks, drag-drop, JSON storage |
| Media (Curator) | ✅ Kompletní | Featured image, galerie, RichEditor plugin, tagging/folders |
| Uživatelé | ✅ Kompletní | Auth, role, 2FA, profil, soft deletes |
| Revize | ✅ Kompletní | Auto-versioning, max 50/entry, diff |
| Installer | ✅ Kompletní | `php artisan mipresscz:install` |
| Test suite | ✅ Silná | 497 testů; mezery pouze frontend rendering |
| SEO (meta, hreflang, sitemap, canonical) | ✅ Kompletní | Meta tagy, hreflang, sitemap.xml, feed.xml, canonical URL |
| Fulltext vyhledávání | ✅ Kompletní | Laravel Scout + database driver |
| Menu builder | ✅ Kompletní | Drag & drop, víceúrovňové, 3 typy položek |
| Dashboard widgety | ✅ Kompletní | Počty entries, poslední aktivita |
| Entry preview | ✅ Kompletní | Token-based preview před publikací |
| Caching | ✅ Kompletní | CacheService, PageCache, auto-invalidace |
| Computed data | ✅ Kompletní | ComputedFieldRegistry, word_count, reading_time |
| Error pages | ✅ Kompletní | 404, 500, 503; dark mode, i18n |
| Security headers | ✅ Kompletní | CSP, HSTS, X-Frame-Options, Referrer-Policy |
| CI/CD | ✅ Kompletní | GitHub Actions (lint, testy, install smoke) |
| Nastavení webu | ✅ Kompletní | ManageSiteSettings (homepage, budoucí rozšíření) |
| Frontend rendering | ⚠️ Základ | Fallback šablona, žádný produkční layout |
| Uživatelská dokumentace | ✅ Kompletní | `docs/admin-guide.md` — příručka administrátora |
| Editorial workflow (Working Copy) | ❌ Plánováno | Fáze 11 |
| API vrstva (headless/REST) | ❌ Plánováno | P2 backlog |

---

## Celkové hodnocení: 9.0 / 10

| Dimenze | Skóre |
|---------|-------|
| Architektura | 9/10 |
| Content systém | 10/10 |
| Admin panel | 9/10 |
| Testové pokrytí | 9/10 |
| Frontend | 5/10 |
| Dokumentace | 9/10 |
| Produkční připravenost | 8/10 |

Projekt má výborný základ — čistá architektura, modulární core package, kompletní content model, solidní test suite (497 testů). SEO, vyhledávání, menu builder, preview, caching a security hardening jsou implementovány. Hlavní investiční příležitosti směřují do frontend šablony, uživatelské dokumentace a editorial workflow (Working Copy).

### Rizika

- změny brick struktur mohou mít zpětně nekompatibilní dopad na data,
- při změnách v admin theme je nutné hlídat `@source` a plugin CSS,
- při rozšiřování frontendu bude důležitější správně hlídat public/private media visibility,
- Working Copy (Fáze 11) bude vyžadovat pečlivý návrh, aby nenarushil stávající revizi a preview workflow.

### Doporučení

1. Investovat do produkční frontend šablony a layout systému.
2. Vytvořit uživatelské dokumentace (admin UX guide) před pilotním nasazením.
3. Při návrhu Working Copy workflow respektovat existující event páry a cache invalidaci.

## Seedery a referenční data

`DatabaseSeeder` spouští tyto seedy:

- `RolesAndPermissionsSeeder`
- `LocaleSeeder`
- `GlobalsSeeder`
- `ContentSeeder`

Seedery tu neslouží jen jako bootstrap. Jsou zároveň referenční ukázkou očekávaného datového modelu projektu.

### Důsledky

- refaktory doménových modelů musí počítat i s dopadem do seed dat,
- seedery by měly zůstat idempotentní a čitelné,
- pokud bude demo obsah dál růst, vyplatí se ho rozdělit do menších tematických seederů.

## Authorization a role model

Role model je postavený nad `UserRole` enumem a Spatie Permission.

### Co funguje dobře

- role jsou explicitní a srozumitelné,
- superadmin bypass přes `Gate::before()` je jednoduchý,
- vazba role -> permissions je čitelná.

### Rizika

- permissions jsou rozdělené mezi enum, policy logiku a seedery,
- při změnách hrozí drift mezi těmito vrstvami,
- contributor-specific omezení nad entries může časem růst do složitějšího business pravidla.

### Doporučení

1. Při změně oprávnění vždy validovat enum, policy i seed data současně.
2. Přidávat behaviorální testy pro autorizaci důležitých workflow.

## Testování a kvalita

Projekt používá Pest 4 a MySQL test databázi `mipresscz_testing`.

### Ověřený stav

- `tests/Pest.php` aplikuje `RefreshDatabase` pro Feature testy,
- testy pokrývají content system, entry routing, locale workflow, locale observer, locale service a roles/permissions,
- testovací nastavení odpovídá reálnějšímu MySQL chování, ne SQLite-only variantě,
- aktuálně **185 testů, 371 assertions** — všechny zelené.

### Rizika

- Filament resource interakce a admin UX nejsou pokryté tak dobře jako backend doména a locale systém,
- s růstem admin funkcí může bez Livewire/Filament testů přibývat tichých regresí.

### Doporučení

1. Priorita dalšího testování: admin resources, policy flows, locale admin page.
2. U průřezových změn preferovat feature testy nad izolovanými unit testy.

## Dokumentace a governance

Repo už jednou narazilo na dokumentační drift. To je explicitní provozní poučení.

### Aktuální pravidla

- README slouží pro onboarding a rychlou orientaci,
- tento dokument je hlavní živá analýza projektu,
- architektonické změny mají být doprovázené změnou dokumentace v `docs/`.

### Rizika

- bez disciplíny se vrátí rozpad mezi realitou v kódu a markdown dokumentací,
- staré snapshoty analýz zvyšují šum a zhoršují onboarding.

### Doporučení

1. Držet jen jeden hlavní analytický dokument.
2. Doplňovat samostatně jen roadmapu nebo úzce zaměřené technické návrhy, ne další paralelní obecné analýzy.

## Hlavní silné stránky

- Dobře čitelný doménový model.
- Smysluplné využití Filament 5 bez vendor hackování.
- Databázově řízená lokalizace.
- Připravená admin integrace pro média i block builder.
- Funkční testovací základ nad realistickým MySQL prostředím.

## Hlavní rizika

### Vysoká priorita

1. Regrese v locale a URL logice kvůli průřezovým závislostem.
2. Skrytý dopad změn collections na dynamické entry resources a admin navigaci.

### Střední priorita

1. Růst komplexity `Entry` modelu (částečně zmirněno traity HasOrigin, ContainsComputedData).
2. Dokumentační drift mezi kódem a dokumentací.
3. Budoucí frontend nekonzistence při rychlém rozšiřování UI.

### Nižší priorita

1. Default Laravel metadata v `composer.json`.
2. Ne vždy zcela jasná role všech instalovaných balíčků bez průběžně psané usage dokumentace.

## Doporučený způsob práce v tomto repu

1. Za zdroj pravdy ber kód a aktuální dokumentaci v `docs/`.
2. Při zásahu do entries vždy ověř dopad na dynamické resource konfigurace.
3. Při zásahu do locale workflow měň společně službu, middleware, URL generování, admin správu a testy.
4. Při admin UI zásazích zkontroluj `AdminPanelProvider`, render hooky a existující panel customizace.
5. Po architektonické změně aktualizuj README i tento dokument.

## Závěr

miPress je ve stavu produkčně připraveného CMS jádra se zdravou doménovou architekturou, kompletním admin panelem a silným testovým pokrytím (497 testů). Všechny klíčové CMS funkce jsou implementovány: SEO, vyhledávání, menu builder, preview, caching, security, computed data. Největší technické riziko neleží v jednotlivých souborech, ale v místech, kde se propojuje více vrstev najednou: locale workflow, dynamické admin resources a budoucí růst frontendu. Další kroky směřují k uživatelské dokumentaci (Fáze 10 P5), editorial workflow Working Copy (Fáze 11) a produkční frontend šabloně.

---

## Hloubková revize a stav oprav (v0.6.0 → refactor/core-extraction, 9. března 2026)

Tato sekce dokumentuje konkrétní bugy a nedostatky nalezené v revizi po Fázi 7 a jejich aktuální stav.

### Stav nálezů

| Priorita | Nález | Stav |
|---|---|---|
| 🔴 | Překlady `content.*` nefungují v Admin UI | ✅ OPRAVENO (a598cd0) |
| 🟠 | Hardcoded nepřeložené stringy | ✅ OPRAVENO (a598cd0) |
| 🟠 | Osiřelá funkce Blocks | ✅ OPRAVENO (a1e2efb) |
| 🟠 | Model `Term` bez Policy | ✅ OPRAVENO (Fáze 7.1) |
| 🟡 | Duplicitní registrace policies | ✅ OPRAVENO (0cafd13) |
| 🟡 | Stub soubory v `app/Enums/` | ✅ OPRAVENO (a598cd0) |
| 🟡 | IDE false positive chyby | ✅ OPRAVENO (77834ed, f1d960f) |
| 🟢 | Uncached Filament assets | ❌ OTEVŘENO (produkce) |
| 🟢 | Breezy překlady | ❌ OTEVŘENO |

### ✅ Opravené nálezy

**🔴 Translation loader race condition** (opraveno v a598cd0)
`addPath()` přesunuto do `register()` přes `callAfterResolving()`. Všechny `content.*` překlady nyní fungují bez ohledu na pořadí bootování providerů.

**🟠 Hardcoded stringy** (opraveno v a598cd0)
`is_pinned`, Curator `label`/`pluralLabel`, Breezy `myProfile` — vše nahrazeno `__()` voláním. Přidány klíče do `content.php` a nový soubor `panel.php` (cs + en).

**🟠 Blokový builder / blocks** (opraveno v a1e2efb)
Bloky nahrazeny Mason pluginem. Přidána migrace `drop_blocks_table`, vyčištěny permissions a překlady.

**🟡 Duplicitní policy registrace** (opraveno v 0cafd13)
`Gate::policy()` volání přesunuta výhradně do `MiPressCzCoreServiceProvider`. `AppServiceProvider` odstraněn od všech content policy registrací.

**🟡 Stub soubory** (opraveno v a598cd0)
`app/Enums/{DateBehavior,DefaultStatus,EntryStatus}.php` smazány. Enums žijí v core.

**🟡 IDE false positives** (opraveno v 77834ed, f1d960f)
`@var Blueprint $blueprint` type hinty v `EntryForm`, helper funkce v `ManageLocalesTest`.

### ❌ Stále otevřené nálezy

**🟠 VYSOKÉ — `Term` bez Policy**
Model `Term` nemá registrovanou policy. Přidat `TermPolicy` do `packages/mipresscz/core/src/Policies/TermPolicy.php` a registrovat ji v `MiPressCzCoreServiceProvider`.

**🟢 NÍZKÉ — Uncached Filament assets**
`php artisan about` ukazuje `blade_icons: NOT CACHED` a `panel_components: NOT CACHED`. Pro produkci přidat do deployment pipeline:
```bash
php artisan icons:cache
php artisan filament:cache-components
```

**🟢 NÍZKÉ — Breezy překlady**
Breezy Sessions page není lokalizovaná do češtiny. `php artisan vendor:publish --tag=filament-breezy-translations`

---

### 🏗️ Architektonická konsolidace modelů (commit 0cafd13, 9. března 2026)

Výsledek refactoru z branch `refactor/core-extraction`:

- `app/Models/` nyní obsahuje **pouze `User.php`** — 8 wrapper modelů smazáno
- `database/factories/` obsahuje **pouze `UserFactory.php`** — 7 duplikátních továren smazáno
- Všechny content modely žijí výhradně v `packages/mipresscz/core/src/Models/`
- Všechny core factories žijí v `packages/mipresscz/core/database/factories/`
- `MiPressCzCoreServiceProvider` registruje: observers, policies, `Factory::guessFactoryNamesUsing()`
- `AppServiceProvider` zredukován na: `Gate::before()`, Table config defaults, LanguageSwitch config
- Všechny test soubory aktualizovány na `MiPressCz\Core\Models\*` namespace
- **185 testů zelených**

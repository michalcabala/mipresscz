# miPress CMS — Project Analysis

Datum: 15. března 2026

## Účel dokumentu

Tento dokument je aktuální technický snapshot projektu miPress. Shrnuje ověřený stav kódu, hlavní silné stránky, rizika a to, co ještě chybí k čistému produkčnímu release.

Nejde o historický deník. Jde o zdroj pravdy pro technická rozhodnutí a release plán.

## Executive summary

miPress je dnes už technicky vyspělé CMS nad Laravel 12 a Filament 5. Jádro je extrahované do package-first architektury, admin panel pokrývá hlavní redakční workflow, locale logika je databázově řízená a test suite je široká a aktuálně plně zelená.

Projekt je výrazně blíž produkčnímu nasazení než běžný interní prototyp. Největší riziko už neleží v absenci funkcí, ale v release disciplíně, provozním runbooku a v tom, aby veřejný frontend i redakční UX prošly skutečným pilotním ověřením.

## Ověřený snapshot

### Runtime a balíčky

- PHP 8.3.4
- Laravel 12.54.1
- Filament 5.3.5
- Livewire 4.2.1
- Tailwind CSS 4.2.1
- MySQL jako primární i testovací databáze

### Struktura aplikace

- Tenká app vrstva v root projektu
- CMS jádro v `packages/mipresscz/core`
- Filament admin na `/mpcp`
- `54` ne-vendor routes v aplikaci
- default template systém v `resources/views/templates/default/`

### Kvalita a baseline

- CI workflow: lint, testy, install smoke test
- Plná test suite: `574 passed, 0 failed`
- Scheduler definovaný v [routes/console.php](routes/console.php) pro sitemap generování ve `02:00`

## Co je dnes hotové

## 1. Doménové CMS jádro

Projekt stojí na čistém obsahovém modelu:

- Collections
- Blueprints
- Entries
- Taxonomies a Terms
- Global Sets
- Revisions
- Locales

Silné stránky:

- ULID identifikátory na content modelech
- oddělení app vs. core vrstvy
- soft deletes na hlavních modelech
- enum-based workflow a nastavení
- localization přes `origin_id` a specializované concerns

Tahle vrstva je už dostatečně modulární pro další růst a současně dost konkrétní na skutečné nasazení.

## 2. Filament admin

Admin je dnes jedna z nejsilnějších částí projektu.

Ověřeně funguje:

- dynamická registrace entries resources podle aktivních kolekcí
- dynamická term resources podle taxonomií
- slideover filtry a slideover správa sloupců
- reorder entries pro scoped kolekce
- taxonomy-aware columns a filters
- locale management přes samostatnou page
- media management přes Curator
- Mason block builder v editoru obsahu
- dashboard widgets, site settings, templates, sitemap pages

To je nadprůměrně silný základ pro první produkční release.

## 3. Lokalizace, routing a SEO

Locale a URL subsystém je už funkčně kompletní.

Hotové oblasti:

- single-language a multi-language routing
- prefixované i neprefixované URL chování
- přesměrování v single-language režimu
- locale-aware search routes
- canonical URL
- hreflang tagy
- preview token routes
- `robots.txt`, `sitemap.xml`, `feed.xml`

Z pohledu produkce je to silná konkurenční vlastnost projektu.

## 4. Editorial workflow

Working Copy workflow je implementované a testované.

To znamená, že projekt už není jen CRUD admin, ale skutečné redakční CMS s rozpracovanou verzí obsahu, publikací a návratem do konzistentního stavu.

## 5. Frontend template vrstva

Projekt už nemá jen fallback view. Existuje default template systém s těmito výstupy:

- homepage
- page detail
- article detail
- archive
- 404
- 500
- 503

To je dostatečný základ pro pilotní nasazení. Není to ale ještě totéž co formálně odladěná produkční prezentační vrstva.

## Architektura

## Core vs. app

Architektura je dobře rozdělená.

Do core patří:

- content modely
- services
- observers
- policies
- Filament resources a pages
- installer command
- migrations, factories, seeders
- sdílené překlady a views

Do app vrstvy patří:

- `User` model
- branding a assety
- app-specific Filament pages
- custom Mason bricks
- app-level provider wiring

To je správné rozhodnutí i směrem k budoucí distribuci a `v1` stabilizaci.

## Klíčové technické vzory

### Dynamic resource pattern

`EntryResource` a `TermResource` používají konfigurovatelné resource varianty. To dává projektu flexibilitu bez duplikace resource tříd.

Výhoda:

- jedna logika pro více kolekcí a taxonomií

Cena:

- změny v collections, navigation a routách mají průřezový dopad

Tento pattern je správný, ale vyžaduje vysokou disciplinu v testech a dokumentaci.

### Database-driven locale behavior

Locale rozhodování je centralizované přes modely a služby, ne přes pevnou konfiguraci v kódu. To je správně pro CMS, ale zároveň to dělá locale vrstvu citlivou na regresní změny.

### Package-first governance

Core package je realistický základ pro další evoluci projektu. Tady je důležité nepokazit release disciplínu a verzování.

## Produkční připravenost

## Co už je produkčně silné

- content model a data lifecycle
- admin panel a redakční workflow
- locale routing a SEO vrstva
- preview, search, caching, menu builder
- test suite nad MySQL
- CI s install smoke testem

## Co ještě chybí k čisté `v1.0.0`

### 1. Release governance

Tady je dnes největší mezera.

Konkrétně:

- `packages/mipresscz/core/composer.json` je stále na `0.6.0`
- changelog nekopíruje skutečný rozsah dokončených funkcí
- část starší dokumentace historicky mluví o jiných milnících než odpovídá současný kód

To není runtime problém, ale je to problém důvěryhodnosti release procesu.

### 2. Provozní runbook

Projekt má technické předpoklady pro nasazení, ale chybí explicitní operativní dokument pro:

- deploy
- rollback
- cache warmup
- scheduler a queue dohled
- backup a restore
- smoke ověření po deploy

### 3. Frontend QA

Default template je použitelná, ale není zatím formálně potvrzená jako „produkční theme baseline“.

Chybí hlavně:

- accessibility pass
- obsahové edge-case QA
- responzivní kontrola na všech klíčových šablonách
- pilotní ověření s reálným obsahem

### 4. Browser/E2E vrstva

Feature testy jsou silné, ale browser smoke pokrytí chybí. To je hlavně produkční riziko pro:

- Filament modal/slideover flow
- media picker interakce
- Working Copy UX
- locale switching v adminu

## Hlavní rizika

## Vysoká priorita

### Release/documentation drift

Kód je dál než release metadata. To je teď největší organizační dluh projektu.

### Locale a URL logika

Je silná, ale průřezová. Změna v jedné vrstvě často zasáhne model, service, middleware, controller i frontend rendering.

### Dynamic Filament resources

Tento pattern je správný, ale citlivý na změny v navigaci, route naming a konfiguraci collections/taxonomií.

## Střední priorita

### Frontend theme maturity

Default template existuje, ale není ještě potvrzená jako plně produkční design baseline.

### Chybějící browser smoke tests

Backend je výborně pokrytý, interakční vrstva méně.

### Operační standardizace

CI existuje, ale provozní playbook ještě není sepsaný.

## Nízká priorita

### README a menší dokumentační detaily

README je už použitelný, ale dál je třeba hlídat, aby se nerozjel proti detailnější dokumentaci v `docs/`.

## Silné stránky projektu

- Dobře navržené package-first jádro.
- Vysoká hustota hotových CMS funkcí na relativně kompaktní codebase.
- Ověřený locale a SEO subsystém.
- Funkční redakční workflow včetně Working Copy.
- Silný Filament admin bez vendor hacků.
- Reálně použitelný default template systém.
- Široká a aktuálně zelená test suite.

## Slabší místa projektu

- Release metadata a changelog neodpovídají reálnému stavu funkcí.
- Chybí produkční runbook.
- Chybí browser smoke vrstva.
- Frontend není ještě formálně uzavřený jako produkční baseline.

## Hodnocení

| Oblast | Hodnocení |
|---|---|
| Architektura | 9/10 |
| Content model | 9.5/10 |
| Filament admin | 9/10 |
| Locale a SEO | 9.5/10 |
| Testy a CI | 9/10 |
| Frontend připravenost | 7/10 |
| Release governance | 6/10 |
| Celková produkční připravenost | 8.3/10 |

## Doporučení na další postup

1. Nejprve srovnat release metadata, verzi core a changelog.
2. Sepsat deploy a rollback runbook včetně scheduler/cache kroků.
3. Udělat frontend QA a redakční pilotní UAT.
4. Dodat lehkou browser smoke vrstvu pro kritické admin flow.
5. Teprve potom uzavírat `v1.0.0`.

## Závěr

miPress už dnes není „rozpracovaný CMS nápad“, ale robustní produktový základ s jasnou architekturou, širokým rozsahem hotových funkcí a velmi dobrou testovací disciplínou. To, co dnes nejvíc chybí do produkční verze, není další velká funkce, ale release a provozní tvrdost: sladěné verzování, runbook, pilotní ověření a čistý Go/No-Go proces.

Technicky je projekt připravený postoupit do release kandidátní fáze. Organizačně a provozně je potřeba ještě jeden disciplinovaný krok.

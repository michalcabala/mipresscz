# miPress CMS — Project Roadmap

Datum: 15. března 2026

## Cíl roadmapy

Posunout miPress z technicky silného interního CMS základu do stavu stabilní produkční verze se srozumitelným release procesem, provozním runbookem a pilotně ověřeným frontendem i redakčním workflow.

Projekt už má hotové jádro. Roadmapa níže proto není seznam funkcí, které „by se mohly hodit“, ale konkrétní plán k vydání `v1.0.0`.

## Aktuální baseline

- `packages/mipresscz/core` je extrahované a aplikace na něm běží.
- Admin panel na `/mpcp` funguje nad dynamickými Entry/Term resources.
- SEO, preview, search, caching, menu builder, Working Copy a locale workflow jsou implementované.
- CI pipeline pokrývá lint, MySQL testy a install smoke test.
- Plná test suite je aktuálně zelená: `574 passed, 0 failed`.

## Co ještě brání produkčnímu vydání

### Release a governance mezery

- Chybí jednotný release gate checklist před tagem `v1.0.0`.
- Core package má stále verzi `0.6.0`, zatímco část dokumentace historicky mluví o novějších milnících. Versioning a changelog nejsou synchronizované s reálným stavem funkcí.
- Chybí jednoznačný proces pro release notes, upgrade notes a rollback.

### Provozní mezery

- Není sepsaný produkční runbook pro deploy, rollback, cache warmup, icons cache, Filament component cache a DB restore.
- Scheduler je pro sitemap kritický, ale není explicitně součástí release gate.
- Chybí standardizovaný post-deploy smoke test mimo CI install smoke.

### Produktové mezery

- Frontend má použitelnou default šablonu, ale ještě neprošel formálním produkčním QA kolem přístupnosti, responzivity a obsahového UAT.
- Working Copy workflow je implementované, ale chybí redakční pilotní ověření nad realistickým obsahem a rolemi.
- Browser/E2E coverage pro klíčové admin user journeys stále chybí.

## Roadmapa k `v1.0.0`

## Fáze A — Release baseline alignment

Cíl: srovnat verze, dokumentaci a release artefakty tak, aby projekt měl jeden zdroj pravdy.

### Úkoly

- [ ] Aktualizovat `packages/mipresscz/core/composer.json` verzi podle skutečného release stavu.
- [ ] Doplnit `packages/mipresscz/core/CHANGELOG.md` o všechny dokončené milníky po `0.6.0`.
- [ ] Vyčistit staré roadmap/analysis tvrzení, která už neodpovídají kódu.
- [ ] Připravit jednoduchou release konvenci: tag, changelog, upgrade notes, smoke test, rollback notes.
- [ ] Rozhodnout, zda bude první veřejný release `0.7.x` nebo rovnou `1.0.0-rc.1`.

### Exit criteria

- [ ] Verze v core package, changelogu a dokumentaci si neodporují.
- [ ] Každý release má definovaný checklist a vlastní release note.

## Fáze B — Production hardening

Cíl: uzavřít provozní mezery, které nejvíc bolí po prvním nasazení.

### Úkoly

- [ ] Sepsat deployment runbook: build, migrate, optimize, icons cache, Filament cache, scheduler, queue, rollback.
- [ ] Dopsat explicitní produkční checklist pro `.env`, storage, mail, cache, queue a scheduler.
- [ ] Přidat post-deploy smoke scénář: login, create entry, publish, preview, frontend render, sitemap dostupnost.
- [ ] Ověřit backup/restore postup na MySQL databázi a media storage.
- [ ] Zvážit základní observability vrstvu: centralizované logy, alerting na výjimky, health check endpoint nebo syntetický monitoring.

### Exit criteria

- [ ] Nasazení na čisté prostředí je reprodukovatelné podle jednoho dokumentu.
- [ ] Rollback je ověřený v praxi, ne jen teoreticky.
- [ ] Scheduler a cache warmup jsou součástí produkčního postupu.

## Fáze C — Frontend readiness

Cíl: dotáhnout veřejný web z „solidního defaultu“ do stavu, který je obhajitelný pro první produkční klientský projekt.

### Úkoly

- [ ] Udělat QA nad default šablonou: homepage, page detail, article detail, archive, search, 404, 500, 503.
- [ ] Zkontrolovat responzivitu a obsahové edge cases pro Mason bloky.
- [ ] Doplnit accessibility pass: headings, focus states, kontrast, landmarky, formulářové popisky.
- [ ] Ověřit image/media strategii: public/private visibility, fallbacky, lazy loading, OG image flow.
- [ ] Rozhodnout, co je součást „produkční default theme“ a co zůstává jen demo/pilotním obsahem.

### Exit criteria

- [ ] Default template je použitelná bez ručního patchování pro první pilotní web.
- [ ] Nejsou otevřené kritické UI chyby na desktopu ani mobilu.

## Fáze D — Editorial a admin acceptance

Cíl: potvrdit, že admin není jen technicky správně, ale i použitelný pro redakci.

### Úkoly

- [ ] Projít UAT scénáře pro role Admin, Editor a Contributor.
- [ ] Ověřit Working Copy workflow: create, draft save, publish, discard, preview, locale verze.
- [ ] Ověřit tabulkové workflow: filtry, slideovery, column manager, reorder entries, taxonomy terms, media picker.
- [ ] Zvážit lehkou browser/E2E vrstvu pro nejdůležitější journeys.
- [ ] Doplnit stručný interní test checklist pro regresní ověření před každým release.

### Exit criteria

- [ ] Redakční pilot neodhalí blocker v admin UX.
- [ ] Kritické admin workflow mají automatizovaný smoke coverage.

## Fáze E — Release candidate a `v1.0.0`

Cíl: převést projekt ze stavu „feature complete“ do stavu „ship-ready“.

### Úkoly

- [ ] Vyhlásit feature freeze pro release kandidáta.
- [ ] Uzavřít kritické a vysoké bugy.
- [ ] Připravit upgrade notes a release notes.
- [ ] Otagovat `v1.0.0-rc.1`, nasadit na staging a provést Go/No-Go review.
- [ ] Po úspěšném pilotu vydat `v1.0.0`.

### Go/No-Go checklist

- [ ] `574+` testů zelených v CI.
- [ ] Lint a install smoke zelené.
- [ ] Žádný otevřený blocker v content, locale, preview, publish nebo media workflow.
- [ ] Ověřený deploy i rollback.
- [ ] Schválený release note a changelog.

## Doporučené pořadí práce

1. Nejdřív dokončit Fázi A a B. Bez release a provozní disciplíny nemá smysl řešit marketingově „v1“.
2. Potom udělat frontend QA a redakční pilot. Tady se objeví skutečné produkční nuance.
3. Až následně zavést RC režim a tagovat stabilní release.

## Doporučený backlog po `v1.0.0`

- API vrstva / headless režim.
- `mipresscz:new-site` bootstrap command.
- Rozšiřitelnost core přes plugin/hook pipeline.
- Selektivní `withoutX()` pattern pro feature toggles.
- Rozšířená observability a metriky.

## Poznámka k údržbě dokumentu

Roadmapa má být krátká a akční. Dokončené historické milníky patří do changelogu a release notes, ne jako stovky řádků starých checkboxů. Po každém větším release je potřeba aktualizovat hlavně:

- aktuální baseline,
- otevřené blocker body,
- nejbližší 1 až 2 fáze.

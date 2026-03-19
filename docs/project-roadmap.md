# miPress CMS - Project Roadmap

Datum: 17. března 2026

## Cíl roadmapy

Dotáhnout miPress z dnešního stavu "stabilní CMS baseline po odstranění revisions" do první produkční verze s:

- zelenou test suite,
- jasným release procesem,
- ověřeným deploy/rollback postupem,
- odladěným frontendem,
- potvrzeným redakčním workflow.

Roadmapa níže není wishlist. Je to doporučené pořadí práce podle ověřeného stavu kódu k 17. 3. 2026.

## Aktuální Baseline

- Revisions / working copy workflow bylo z projektu kompletně odstraněno.
- Publikované entries se teď upravují přímo.
- Core CMS běží z `packages/mipresscz/core`.
- Admin panel na `/mpcp` pokrývá hlavní obsahové workflow.
- Lokalizace, preview, search, feed, sitemap, menu builder, media management a template systém jsou implementované.
- Mason bloky a default theme existují a jsou použitelné.
- Poslední plný běh suite po odstranění revisions:
  - `556` testů prošlo,
  - `1242` assertions,
  - bez pádů.
- Následně byl uklizen warning v `TermResourceTest` a tento test file znovu prošel samostatně.

## Roadmapa K Produkční Verzi

## Fáze 0 - Release Alignment Po Odstranění Revisions

Cíl: srovnat kód, dokumentaci a release artefakty s novým zjednodušeným workflow.

### Úkoly

- [ ] Dokončit dokumentační cleanup po odstranění revisions.
- [ ] Zkontrolovat, že žádná admin navigace, překlad nebo docs už revisions nenabízí jako feature.
- [ ] Srovnat `packages/mipresscz/core/CHANGELOG.md` a release poznámky s novým stavem.
- [ ] Rozhodnout, jestli další milestone bude `0.7.x`, `1.0.0-rc.1` nebo rovnou `1.0.0`.
- [ ] Aktualizovat `packages/mipresscz/core/composer.json` podle skutečného release stavu.

### Exit criteria

- [ ] Kód, changelog a docs si neodporují.
- [ ] Editorial workflow je v projektu popsané už jen jako direct-save workflow.

## Fáze 1 - Production Hardening

Cíl: doplnit chybějící provozní minimum.

### Úkoly

- [ ] Sepsat deploy runbook:
  - build assetů,
  - migrace,
  - cache clear / warmup,
  - Filament optimize/clear,
  - sitemap generation,
  - storage kontrola.
- [ ] Sepsat rollback runbook.
- [ ] Dopsat produkční checklist pro `.env`, DB, mail, cache, session, queue a scheduler.
- [ ] Ověřit zálohu a restore databáze i media storage.
- [ ] Definovat minimální monitoring:
  - health check,
  - log routing,
  - alert na výjimky,
  - kontrola scheduleru.

### Exit criteria

- [ ] Nové prostředí lze nasadit podle jednoho dokumentovaného postupu.
- [ ] Rollback není teoretický, ale ověřený.
- [ ] Scheduler a cache kroky jsou součástí release procesu.

## Fáze 2 - Frontend Readiness

Cíl: udělat z default theme obhajitelný produkční baseline.

### Úkoly

- [ ] Otestovat šablony:
  - homepage,
  - page detail,
  - article detail,
  - archive,
  - search,
  - 404,
  - 500,
  - 503.
- [ ] Udělat responzivní QA na desktop/tablet/mobile.
- [ ] Udělat accessibility pass:
  - heading hierarchy,
  - focus states,
  - kontrast,
  - landmarky,
  - formulářové popisky.
- [ ] Ověřit edge cases pro Mason bloky.
- [ ] Ověřit image fallbacks, OG image flow a lazy loading strategii.
- [ ] Rozhodnout, co je oficiální "default production theme" a co je jen demo obsah.

### Exit criteria

- [ ] Default theme je použitelná bez ručního patchování pro pilotní web.
- [ ] Nejsou otevřené kritické vizuální nebo a11y chyby.

## Fáze 3 - Editorial Acceptance

Cíl: potvrdit, že zjednodušené redakční workflow je použitelné v praxi.

### Úkoly

- [ ] Projít UAT pro role:
  - SuperAdmin,
  - Admin,
  - Editor,
  - Contributor.
- [ ] Ověřit entry workflow:
  - draft,
  - publish,
  - přímá editace publikovaného záznamu,
  - unpublish,
  - preview.
- [ ] Ověřit locales workflow:
  - přepínání,
  - translation variants,
  - URL prefix chování.
- [ ] Ověřit menu workflow:
  - custom link,
  - model link,
  - archive link,
  - nesting,
  - reorder.
- [ ] Ověřit media workflow:
  - upload,
  - tagging,
  - folders,
  - picker použití v entries/globals.

### Exit criteria

- [ ] Redakční pilot neodhalí blocker v hlavních admin flow.
- [ ] Kritické workflow má potvrzené chování v praxi, ne jen v testech.

## Fáze 4 - Browser Smoke Coverage

Cíl: doplnit tenkou vrstvu ochrany proti regresím v interaktivním adminu.

### Úkoly

- [ ] Vybrat browser test framework vhodný pro Laravel/Filament stack.
- [ ] Přidat smoke scénáře alespoň pro:
  - login,
  - create/edit/publish entry,
  - přímou editaci publikovaného entry,
  - menu manager,
  - locale switching,
  - media picker,
  - sitemap settings page.
- [ ] Zařadit smoke browser testy do release checklistu.

### Exit criteria

- [ ] Nejkritičtější Filament a Livewire flow mají automatizované browser smoke coverage.

## Fáze 5 - Release Candidate

Cíl: převést produkt ze stavu "technicky stabilní" do stavu "ready to ship".

### Úkoly

- [ ] Vyhlásit feature freeze.
- [ ] Zavřít všechny kritické a vysoké bugy.
- [ ] Aktualizovat release note a upgrade note.
- [ ] Nasadit release kandidáta na staging.
- [ ] Udělat Go/No-Go review nad:
  - testy,
  - deploy,
  - rollback,
  - frontend QA,
  - editorial UAT.

### Exit criteria

- [ ] Existuje kandidát `v1.0.0-rc.1` nebo ekvivalentní release.
- [ ] Všechny release gate podmínky jsou splněné.

## Fáze 6 - Produkční Release

Cíl: vydat první produkční verzi bez skrytého provozního dluhu.

### Úkoly

- [ ] Otagovat finální release.
- [ ] Nasadit na produkci podle runbooku.
- [ ] Projít post-deploy smoke checklist.
- [ ] Monitorovat prvních 24-72 hodin.
- [ ] Zachytit post-launch incidenty a převést je do backlogu.

### Go/No-Go Checklist

- [ ] Celá test suite je zelená.
- [ ] Neexistuje otevřený blocker v entries, locales, menu nebo media.
- [ ] Deploy i rollback jsou ověřené.
- [ ] Frontend baseline je odladěná pro mobil i desktop.
- [ ] Release artefakty jsou synchronizované.

## Doporučené Pořadí

1. Nejprve Fáze 0. Po odstranění revisions je potřeba srovnat release contract a dokumentaci.
2. Hned potom Fáze 1. Provozní disciplína je teď důležitější než další velká feature práce.
3. Následně Fáze 2 a 3. Tam se odhalí skutečné produkční nuance i reálná použitelnost redakčního workflow.
4. Před tagem doplnit Fázi 4. Browser smoke testy nejsou nutné pro vývoj, ale jsou velmi vhodné před prvním veřejným releasem.
5. Teprve pak RC a produkce.

## Prioritizovaný Backlog Po `v1.0.0`

### P1

- Headless API vrstva.
- Lepší observability a monitoring.
- Více než jedna produkčně odladěná frontend theme.

### P2

- Rozšiřitelný plugin / hook systém nad core.
- Bootstrap command pro založení nového webu.
- Další content blocky a editor productivity features.

### P3

- Multi-site / tenancy úvahy.
- Import/export workflow.
- Vyspělejší content governance a publishing rules.

## Shrnutí

Nejkratší cesta k produkční verzi už není řešit rozbitý revisions workspace. Ten byl odstraněn. Další cesta je teď jasnější:

1. srovnat release metadata a dokumentaci,
2. dopsat deploy a QA disciplínu,
3. potvrdit frontend a redakční použitelnost na pilotu,
4. doplnit browser smoke coverage,
5. teprve pak udělat RC a produkční release.

Pokud se tento sled dodrží, je reálné dostat miPress do prvního produkčního releasu bez skrytého release dluhu.

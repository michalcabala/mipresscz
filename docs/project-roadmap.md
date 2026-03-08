# miPress CMS — Project Roadmap

Datum: 8. března 2026

## Účel dokumentu

Tato roadmapa převádí poznatky z projektové analýzy do konkrétních priorit. Není to backlog každého detailu, ale praktický plán hlavních vývojových proudů projektu.

## Strategické cíle

1. Stabilizovat jádro CMS a minimalizovat regresní rizika v locale, routing a admin vrstvách.
2. Rozšířit frontend z technického základu na plnohodnotnou prezentační vrstvu.
3. Posílit admin UX, test coverage a předvídatelnost dalšího vývoje.
4. Udržet dokumentaci a architekturu konzistentní při růstu projektu.

## Etapa 1 — Stabilizace jádra

### Cíl

Snížit riziko regresí v nejcitlivějších částech systému.

### Prioritní úkoly

- Doplnit testy pro locale URL workflow, zejména kombinace prefix/no-prefix a redirect scénáře.
- Doplnit testy pro `ManageLocales` page a související admin chování.
- Doplnit behaviorální testy pro roles, policies a klíčové authorization scénáře.
- Zkontrolovat a případně zjednodušit nejprůřezovější části `Entry` modelu.

### Výstup

- Vyšší důvěra v locale systém, routing a permission model.

## Etapa 2 — Frontend foundation

### Cíl

Převést současný minimalistický frontend na udržitelný základ pro produkční prezentace.

### Prioritní úkoly

- Zavést hlavní frontend layout a sdílené blade komponenty.
- Standardizovat práci s navigací, patičkou a globálními sadami.
- Navrhnout použití `LanguageSwitcher` ve frontend layoutu.
- Oddělit demo / placeholder obsah od reálného frontend renderingu.

### Výstup

- Jednotná frontend architektura připravená pro další vývoj šablon.

## Etapa 3 — Content experience

### Cíl

Zlepšit editační i renderovací zkušenost s blokovým obsahem a médii.

### Prioritní úkoly

- Rozšířit použití Mason obsahu napříč relevantními blueprinty.
- Ověřit a dopracovat media workflow přes Curator ve formulářích a renderingu.
- Definovat doporučené block patterny pro landing page, články a standardní stránky.
- Otestovat kompatibilitu builder JSON dat při budoucích změnách brick struktur.

### Výstup

- Konzistentní obsahový editor a předvídatelný frontend render.

## Etapa 4 — Admin UX a produktivita

### Cíl

Udržet admin panel rychlý, přehledný a bezpečný při rostoucím objemu funkcí.

### Prioritní úkoly

- Projít klíčové resource tabulky a formuláře z hlediska použitelnosti.
- Doplnit Filament/Livewire testy pro nejčastější admin workflow.
- Udržet všechny panel customizace centralizované v `AdminPanelProvider`.
- Průběžně ověřovat dopad dynamic entry resources na navigaci a oprávnění.

### Výstup

- Stabilnější a lépe testovaný admin panel.

## Etapa 5 — SEO a publikační vrstva

### Cíl

Posílit produkční použitelnost projektu pro reálné weby.

### Prioritní úkoly

- Navrhnout SEO metadata pro entries a případně global sets.
- Vyjasnit canonical a hreflang strategii pro vícejazyčný frontend.
- Doplnit doporučený pattern pro články, seznamové stránky a detail stránky.
- Zvážit základní vyhledávání nebo indexační vrstvu pro obsah.

### Výstup

- Projekt lépe připravený pro reálné nasazení a obsahový marketing.

## Etapa 6 — Governance a maintainability

### Cíl

Zabránit budoucímu technickému a dokumentačnímu driftu.

### Prioritní úkoly

- Udržovat `docs/project-analysis.md` jako jedinou hlavní obecnou analýzu.
- Po větších architektonických změnách aktualizovat README i docs.
- Dokumentovat usage méně zřejmých balíčků, pokud se začnou používat výrazněji.
- Průběžně revidovat, zda se logika v `Entry` a locale vrstvě nerozrůstá příliš koncentrovaně.

### Výstup

- Předvídatelnější vývoj a menší onboarding náklady.

## Prioritizovaný backlog

### Krátkodobě

1. Testy pro locale admin workflow a policy flows.
2. Frontend layout a shared components.
3. Konsolidace Mason + Curator usage v hlavních blueprintech.

### Střednědobě

1. Filament/Livewire test coverage pro hlavní resources.
2. SEO a publikační metadata.
3. Vylepšení frontend navigace a list/detail page patterns.

### Dlouhodobě

1. Vyhledávání nad obsahem.
2. Další modulární oddělení složité entry logiky.
3. Případná API nebo headless vrstva podle produktových potřeb.

## Rozhodovací pravidla

Při plánování dalších změn platí:

1. Nejprve stabilita locale a routing logiky.
2. Potom frontend foundation a editor experience.
3. Teprve potom širší rozvoj nadstavbových funkcí.

## Poznámka k údržbě roadmapy

Roadmapa má být průběžně revidovaná podle reality v kódu a produktových priorit. Nemá suplovat detailní issue tracker, ale držet společný technický směr projektu.

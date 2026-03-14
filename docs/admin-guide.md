# miPress CMS — Příručka administrátora

## Přihlášení

Admin panel je dostupný na adrese `/mpcp`. Přihlaste se e-mailem a heslem. Po přihlášení se zobrazí dashboard s přehledem obsahu.

Pokud máte zapnuté dvoufaktorové ověření (2FA), budete po zadání hesla vyzváni k zadání kódu z autentizační aplikace.

---

## Dashboard

Po přihlášení uvidíte:

- **Přehled záznamů** — 4 karty: celkem záznamů, publikovaných, konceptů, naplánovaných
- **Nejnovější záznamy** — tabulka 10 naposledy upravených záznamů s odkazem na editaci

V pravém horním rohu je odkaz **Zobrazit web** pro rychlý přechod na frontend.

---

## Obsah

### Záznamy (Entries)

Záznamy jsou hlavní obsahové jednotky — stránky, články, produkty apod. Každá kolekce má vlastní sekci v navigaci.

#### Vytvoření záznamu

1. Vyberte kolekci v levé navigaci (např. Stránky, Blog).
2. Klikněte na **Nový záznam**.
3. Vyplňte **Název** — slug se vygeneruje automaticky.
4. Vytvořte obsah v **block editoru** (Mason) — klikněte na `+` a vyberte typ bloku.
5. V postranním panelu nastavte:
   - **Hlavní obrázek** — kliknutím otevřete knihovnu médií
   - **Autor** — předvyplněný aktuální uživatel
   - **Publikováno** — datum a čas publikace
   - **Připnuto** — zobrazení na přednostní pozici
6. Uložte jako **Koncept** (Ctrl+S) nebo rovnou **Publikujte**.

#### Bloky obsahu (Mason)

Obsah se skládá z bloků, které přetahujete a řadíte:

| Blok | Použití |
|------|---------|
| Text | Textový obsah s formátováním |
| Nadpis | Nadpis H1–H6 |
| Obrázek | Jeden obrázek z knihovny médií |
| Galerie | Více obrázků vedle sebe |
| Video | Vložení videa (YouTube, Vimeo) |
| Citát | Zvýrazněný citát |
| Tlačítko | Odkaz ve formě tlačítka |
| Sloupce | Vícesloupcové rozvržení |
| Hero sekce | Velký úvodní blok s obrázkem a textem |
| Vlastnosti | Grid s ikonami a popisy |
| Statistiky | Čísla a metriky |
| Výzva k akci | CTA blok s tlačítkem |
| Karty | Kartičky s obsahem |
| Reference | Svědectví / review |
| Nejnovější příspěvky | Automatický výpis posledních záznamů |
| Oddělovač | Vizuální předěl |
| HTML | Vlastní HTML kód |

Dvojklikem na blok otevřete jeho nastavení. Bloky můžete přetahovat pro změnu pořadí.

#### SEO nastavení

V postranním panelu záznamu najdete sekci **SEO** (klikněte pro rozbalení):

- **SEO titulek** — přepisuje výchozí `<title>` tag
- **SEO popis** — meta description pro vyhledávače
- **OG obrázek** — obrázek pro sdílení na sociálních sítích

Pokud pole necháte prázdná, použijí se výchozí hodnoty z názvu a obsahu záznamu.

#### Statistiky obsahu

U existujících záznamů se v postranním panelu zobrazuje sekce **Statistiky obsahu**:

- **Počet slov** — automaticky spočítáno z textového obsahu
- **Doba čtení** — odhadovaná doba čtení (200 slov/min)

#### Jazykové verze

Pokud máte více aktivních jazyků, v horní liště záznamu najdete **přepínač jazyka** (ikona s vlajkou). Můžete:

- **Přepnout** na existující jazykovou verzi
- **Vytvořit** chybějící jazykovou verzi — otevře se nový záznam propojený jako překlad

#### Náhled

U existujících záznamů je v horní liště tlačítko **Náhled**, které otevře záznam v novém okně — i pokud ještě není publikovaný.

#### Stavy záznamu

| Stav | Význam |
|------|--------|
| Koncept | Rozpracovaný, nepublikovaný |
| Publikováno | Viditelný na webu |
| Naplánováno | Publikuje se automaticky v nastaveném datu |

---

### Kolekce

Kolekce definují typy obsahu (Stránky, Blog, Novinky…). Najdete je v navigační skupině **Struktura**.

Každá kolekce má:

- **Název** a **handle** (technické ID)
- **Směrování** — prefix URL adresy záznamů
- **Řazení** — výchozí řazení záznamů
- **Stromová struktura** — zapnutí nadřazených/podřazených záznamů
- **Stav** — aktivní/neaktivní (neaktivní kolekce se nezobrazují v navigaci)
- **Vazby na blueprinty a taxonomie**
- **Lokalizované názvy** — přepínačem jazyka nastavíte název kolekce v každém jazyce

### Blueprinty

Blueprinty definují strukturu polí pro záznamy. Najdete je pod kolekcí ve **Struktuře**.

Každý blueprint obsahuje:

- **Definice polí** — repeatery s konfigurací: handle, typ pole, sekce (main/sidebar), šířka, povinnost, přeložitelnost
- Jeden blueprint může být sdílený více kolekcemi

### Taxonomie a termíny

Taxonomie slouží k kategorizaci obsahu (Kategorie, Tagy, Témata). Spravují se ve **Struktuře**.

- **Taxonomie** definuje skupinu (např. Kategorie)
- **Termíny** jsou jednotlivé položky (např. Technologie, Sport)
- Termíny mohou být **hierarchické** (rodič → potomek)
- Každý termín existuje **per jazyk** — při vytváření vyberte jazyk a případně navažte na originální termín

### Globální nastavení

Globální nastavení (GlobalSets) ukládají data sdílená napříč celým webem — kontaktní údaje, nastavení patičky, sociální sítě.

- Spravují se ve **Struktuře → Globální nastavení**
- Každá sada má **handle**, pomocí něhož se na data odkazujete v šablonách
- Hodnoty jsou uložené jako **klíč-hodnota** páry
- Globální sady podporují **jazykové verze**

---

## Média

### Knihovna médií

Média (obrázky, dokumenty, videa) spravujete v sekci **Média**. Knihovna médií se otevírá také při výběru obrázku v záznamu.

- **Upload** — přetáhněte soubor nebo klikněte na Upload
- **Vyhledávání** — filtrujte podle názvu, složky nebo štítku

### Složky

Média můžete organizovat do **složek** (Média → Složky):

- Stromová struktura až 5 úrovní hloubky
- Přetahování složek pro přeuspořádání
- Každé médium může být přiřazeno do jedné složky

### Štítky

Pro další organizaci použijte **štítky** (Média → Štítky):

- Jedno médium může mít více štítků
- Štítky lze vytvářet přímo při úpravě média
- V knihovně médií lze filtrovat podle štítků

---

## Navigace (Menu)

Menu se spravuje v sekci **Navigace → Menu**.

### Dostupné lokace

- **Hlavní navigace** (`primary`) — hlavní menu webu
- **Patičková navigace** (`footer`) — menu v patičce

### Typy položek

| Typ | Popis |
|-----|-------|
| Vlastní odkaz | Libovolná URL adresa |
| Záznam | Odkaz na existující záznam (stránku, článek) |

### Správa menu

1. Vyberte menu lokaci.
2. Klikněte na **Přidat položku**.
3. Vyberte typ (vlastní odkaz nebo záznam).
4. Vyplňte popisek a URL / vyberte záznam.
5. Nastavte, zda se má odkaz otevřít v nové záložce.
6. Uložte.

Položky menu můžete přetahovat pro změnu pořadí a vnořování.

---

## Nastavení

### Nastavení webu

V sekci **Nastavení → Nastavení webu** najdete:

- **Domovská stránka** — vyberte záznam z kolekce Stránky, který bude sloužit jako homepage
- **Vyprázdnit cache** — smaže všechny cachované stránky, navigaci a další data

### Jazyky

V sekci **Nastavení → Jazyky** spravujete jazykové mutace webu:

- **Přidání jazyka** — kód, název, vlajka, URL prefix
- **Výchozí jazyk** — jeden jazyk musí být výchozí (nelze smazat)
- **Dostupnost** — samostatně pro admin panel a frontend
- **Směr písma** — LTR/RTL
- **Záložní jazyk** — jazyk použitý při chybějícím překladu

Při jednom aktivním frontend jazyce se URL prefixy nepoužívají. Při více jazycích se automaticky přidají.

### Šablony webu

V sekci **Nastavení → Šablony webu** vidíte dostupné šablony pro frontend a můžete aktivovat vybranou.

---

## Uživatelé

Uživatelé se spravují v sekci **Správa → Uživatelé**.

### Role

| Role | Oprávnění |
|------|-----------|
| Super Administrátor | Plný přístup ke všemu |
| Administrátor | Správa uživatelů, kolekcí, záznamů, taxonomií, globálních sad, menu |
| Redaktor | Prohlížení uživatelů/kolekcí/globálních sad, CRUD záznamů, správa taxonomií/menu |
| Přispěvatel | Prohlížení a tvorba/úprava vlastních záznamů, prohlížení taxonomií |

### Operace

- **Vytvoření** — jméno, e-mail, heslo, role
- **Úprava** — změna údajů a role
- **Smazání** — soft delete (záznam se přesune do koše)
- **Obnovení** — obnovení ze smazaných
- **Trvalé smazání** — nevratné odstranění
- Vlastní účet nelze smazat.

### Profil a 2FA

Kliknutím na **Můj profil** v uživatelském menu (pravý horní roh) můžete:

- Změnit jméno, e-mail, heslo
- Zapnout/vypnout **dvoufaktorové ověření** (2FA)
- Spravovat aktivní přihlášení (prohlížeče)

---

## Vyhledávání

V horní liště admin panelu je **globální vyhledávání**. Stačí začít psát a vyhledává se napříč:

- Záznamy (podle názvu)
- Kolekce, blueprinty, taxonomie, termíny
- Globální sady
- Média (složky, štítky)
- Uživatelé

V tabulkách záznamů je navíc sloupcové vyhledávání a filtry podle stavu a jazyka.

---

## Klávesové zkratky

| Zkratka | Akce |
|---------|------|
| Ctrl+S | Uložit záznam / formulář |

---

## Tipy

- **Naplánovaný obsah** — nastavte datum publikace do budoucna a uložte jako Naplánováno. Záznam se automaticky zobrazí ve stanoveném čase.
- **Připnuté záznamy** — zapněte přepínač „Připnuto" pro zobrazení záznamu na přednostní pozici.
- **Hierarchie** — u kolekcí se zapnutou stromovou strukturou můžete nastavit nadřazený záznam pro vytvoření hierarchie stránek.
- **Hromadné operace** — v tabulce záznamů můžete zaškrtnout více položek a provést hromadnou akci (publikovat, smazat).
- **Cache** — po větších změnách obsahu doporučujeme vyprázdnit cache v Nastavení webu.

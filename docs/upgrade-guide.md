# Upgrade Guide — mipresscz/core

Tento dokument popisuje postup aktualizace mezi verzemi core balíčku.

---

## Versioning

Core sleduje [Semantic Versioning](https://semver.org/):

- **PATCH** (`0.x.Y`) — pouze opravy chyb, žádné API změny.
- **MINOR** (`0.X.0`) — nové funkce, zpětně kompatibilní. Mohou přidat nové public metody, config klíče, nebo volitelné parametry.
- **MAJOR** (`X.0.0`) — breaking changes. Tento dokument popisuje nutné kroky pro každý major skok.

Dokud je verze `< 1.0.0`, může MINOR verze obsahovat breaking changes — změny budou vždy zdokumentovány zde.

---

## Obecný upgrade postup

```bash
# 1. Aktualizovat závislosti
composer update mipresscz/core

# 2. Zkontrolovat nové/změněné migrace
php artisan migrate --no-interaction

# 3. Znovu publikovat config/views pokud jsou přepsány
php artisan vendor:publish --tag=mipresscz-config --force
php artisan vendor:publish --tag=mipresscz-views --force

# 4. Spustit testy
php artisan test --compact

# 5. Vymazat cache
php artisan optimize:clear
php artisan filament:optimize-clear
```

---

## 0.5.0 → 0.6.0

### Žádné breaking changes

Tato verze přidává pouze testy. Žádné migrace, žádné API změny.

---

## 0.4.0 → 0.5.0

### Žádné breaking changes

Přidán `mipresscz:install` artisan command. Žádné změny existujícího API.

---

## 0.3.0 → 0.4.0

### Změna: routing soubor v core

Core nyní registruje frontend routes sám přes `$this->app->booted()`. App `routes/web.php` by neměl duplicitně registrovat catch-all entry routy.

**Akce:** zkontrolovat `routes/web.php` a odstranit routy, které jsou nyní v core `routes/web.php`.

---

## 0.2.0 → 0.3.0

### Přesun modelů do core namespace

Všechny content modely byly přesunuty z `App\Models\` do `MiPressCz\Core\Models\`. App `app/Models/` obsahuje proxy třídy pro zpětnou kompatibilitu.

**Akce:** pokud přistupujete na modely přímo přes `MiPressCz\Core\Models\`, nic neměňte. Pokud máte vlastní kód odkazující na `App\Models\Collection` atd., zkontrolujte, zda proxy třídy v app vrstvě stačí, nebo importy aktualizujte.

### Přesun policies

Content policies jsou nyní registrovány v `MiPressCzCoreServiceProvider`. Odstraňte duplicitní `Gate::policy()` registrace z `AppServiceProvider`.

---

## 0.1.0 → 0.2.0

### Nový `MiPressCzAdminPanelProvider`

Core nyní poskytuje base panel provider. App `AdminPanelProvider` musí dědit z `MiPressCz\Core\Providers\Filament\MiPressCzAdminPanelProvider` místo z `Filament\Panel\PanelServiceProvider`.

**Akce:**

```php
// Před (0.1.x)
class AdminPanelProvider extends PanelServiceProvider { ... }

// Po (0.2.x)
class AdminPanelProvider extends MiPressCzAdminPanelProvider { ... }
```

Přesuňte plugin konfiguraci (Curator, Breezy, LanguageSwitch) do hook metod base provideru nebo je odstraňte, pokud je core hodnoty vyhovují.

---

## Migrace databáze

Core migrace jsou publikovány tagem `mipresscz-migrations`. Nikdy neupravujte publikované migrace — místo toho vytvořte novou migraci v root projektu, která přidá/změní sloupce.

```bash
# Publikovat core migrace (pouze pokud je přebíráte do projektu)
php artisan vendor:publish --tag=mipresscz-migrations

# Spustit čekající migrace
php artisan migrate --no-interaction
```

---

## Breaking changes policy (od 1.0.0)

Od verze `1.0.0` platí:

1. **Public API modelů** (public metody, scopes, relationships) — breaking changes pouze v MAJOR.
2. **Filament resources** — breaking changes v MINOR jsou možné dokud je panel považován za interní. Od 1.0.0 jen v MAJOR.
3. **Config klíče** — přidání nových klíčů je MINOR, přejmenování/odebrání je MAJOR.
4. **Migrace** — nové sloupce jsou MINOR, odebrání/přejmenování je MAJOR.
5. **Artisan commands** — nové příkazy jsou MINOR, změna signatury je MAJOR.

---

## Oznámení breaking changes

Breaking changes budou vždy:

- Zdokumentovány v tomto souboru s explicitním postupem.
- Uvedeny v `CHANGELOG.md` pod odpovídající verzí.
- Označeny v git tagu.

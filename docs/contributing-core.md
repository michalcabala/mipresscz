# Contributing to mipresscz/core

Tento dokument popisuje workflow pro přispívání do `packages/mipresscz/core`.

---

## Předpoklady

- PHP 8.3+, Composer 2
- Laravel Herd (projekt běží na `mipresscz.test`)
- MySQL s databází `mipresscz_testing` pro testy
- Node.js + npm (Vite build pro Filament theme)

---

## Nastavení vývojového prostředí

```bash
git clone <repo>
cd mipresscz
composer install
npm install
cp .env.example .env
php artisan key:generate --no-interaction
php artisan migrate --no-interaction
php artisan db:seed --no-interaction
npm run build
```

---

## Workflow pro změny v core

Core balíček sídlí v `packages/mipresscz/core/` a je propojen s root projektem přes Composer path repository. Jakákoli změna v `packages/` je okamžitě viditelná bez nutnosti reinstalace.

### 1. Vytvořit feature branch

```bash
git checkout -b feat/nazev-funkce
# nebo
git checkout -b fix/nazev-opravy
```

### 2. Implementovat změnu

Dodržujte konvence z `.github/copilot-instructions.md`:

- Typy PHP 8.3+, PSR-12, typ hints na všech metodách.
- UI texty přes `__()` — primárně `cs`, fallback `en`.
- DB sloupce a kód v angličtině.
- Modely v `packages/mipresscz/core/src/Models/`, enums v `Enums/`.
- Filament resources dodržují split pattern: `Resource.php` → `Schemas/`, `Tables/`, `Pages/`.

### 3. Napsat testy

Každá nová třída, model, service nebo seeder musí mít odpovídající test.

```bash
php artisan make:test --pest NazevTestu
```

- Feature testy do `tests/Feature/`, unit testy do `tests/Unit/`.
- Testy používají MySQL databázi `mipresscz_testing` — ne SQLite.
- `RefreshDatabase` se aplikuje automaticky na Feature testy přes `tests/Pest.php`.

### 4. Spustit testy a lint

```bash
# Testy
php artisan test --compact

# Lint
vendor/bin/pint --dirty
```

Všechny testy musí projít, Pint nesmí hlásit chyby.

### 5. Spustit relevantní artisan příkazy

| Akce | Příkaz |
|---|---|
| Nová migrace | `php artisan migrate` |
| Nový seeder | `php artisan db:seed --class=SeederName` |
| Změna konfigurace | `php artisan config:clear` |
| Změna rout | `php artisan route:clear` |
| Filament resource | `php artisan filament:optimize-clear` |
| Nové ikony | `php artisan icons:clear && php artisan icons:cache` |
| Vše | `php artisan optimize:clear` |

### 6. Aktualizovat CHANGELOG

Každá změna musí být zanesena do `packages/mipresscz/core/CHANGELOG.md` pod sekcí `[Unreleased]`.

Formát položky:
```markdown
### Added / Changed / Fixed / Removed
- Stručný popis změny.
```

### 7. Commitovat

```bash
git add -A
git commit -m "feat: popis změny"
```

Typy commit zpráv: `feat`, `fix`, `refactor`, `test`, `chore`, `docs`.

---

## Přidání nové migrace

Nikdy neupravujte existující migrace. Vždy vytvářejte nové:

```bash
php artisan make:migration add_column_to_entries_table --no-interaction
```

Migrace patří do `database/migrations/` v root projektu (ne do `packages/mipresscz/core/database/migrations/`, pokud nejde o migraci součást core distribuce).

---

## Přidání nového modelu do core

```bash
php artisan make:model NazevModelu --no-interaction
# (pak přesunout do packages/mipresscz/core/src/Models/ a upravit namespace)
```

Checklist:
- [ ] ULID primary key (`$incrementing = false`, `$keyType = 'string'`)
- [ ] `$fillable` nebo `$guarded`
- [ ] Relationships s return type hints
- [ ] Casts přes metodu `casts()`
- [ ] Factory v `packages/mipresscz/core/database/factories/`
- [ ] Test v `tests/Feature/`
- [ ] Policy pokud model vyžaduje autorizaci
- [ ] Registrace policy v `MiPressCzCoreServiceProvider`

---

## Přidání nového Filament resource

```bash
php artisan make:filament-resource NazevModelu --generate --no-interaction
# (pak přesunout do packages/mipresscz/core/src/Filament/Resources/ a upravit namespace)
```

Dodržujte split pattern: `Resource.php` deleguje logiku do:
- `Schemas/NazevModeluForm.php` — form schema
- `Tables/NazevModeluTable.php` — table columns + filters + actions
- `Pages/` — List, Create, Edit pages

---

## Přidání překladu

1. Přidat klíč do `packages/mipresscz/core/resources/lang/cs/` (primární).
2. Přidat ekvivalent do `packages/mipresscz/core/resources/lang/en/`.
3. Nikdy nepoužívat hardcoded české/anglické stringy v PHP kódu.

---

## Code review checklist

- [ ] Testy pokrývají happy path i edge cases
- [ ] Všechny PHP testy projdou (`php artisan test --compact`)
- [ ] Pint nehlásí chyby (`vendor/bin/pint --dirty`)
- [ ] CHANGELOG aktualizován
- [ ] Jsou-li breaking changes, dokumentovány v `docs/upgrade-guide.md`
- [ ] Commit zpráva odpovídá konvenci (`feat/fix/refactor/test/chore/docs`)

---

## Release postup

Releasy provádí maintainer:

```bash
# 1. Aktualizovat verzi v packages/mipresscz/core/composer.json
# 2. Přesunout [Unreleased] sekci v CHANGELOG na nové číslo verze s datumem
# 3. Commitovat
git commit -m "chore: release v0.X.0"
# 4. Otagovat
git tag v0.X.0
git push origin v0.X.0
```

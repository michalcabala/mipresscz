# Changelog — mipresscz/core

All notable changes to the core package are documented here.
This project follows [Semantic Versioning](https://semver.org/).

---

## [Unreleased]

### Removed
- Entry revisions, working copy workflow, revision diff utilities, and the revisions workspace were removed from the core package.

### Changed
- Feature tests were aligned to the direct-save editorial workflow for published entries.
- The legacy combined GlobalSet test file was replaced by `GlobalSetModelTest`.

---

## [0.6.0] — 2026-03-09

### Added
- Comprehensive Pest test coverage: Entry model, Collection/Blueprint, Taxonomy/Term, GlobalSet, BlueprintPolicy, UserRole enum (185 definitions total).
- `EntryModelTest` — scopes (draft, root, ordered, published), relationships (terms, relatedEntries, referencedBy, author), URI token generation, slug/uri regeneration, is_pinned cast.
- `CollectionBlueprintModelTest` — SoftDeletes, restore, settings/is_active casts, blueprint→collection/entries relationships, getFieldsBySection ordering.
- `TaxonomyTermModelTest` — taxonomy→terms/collections, SoftDeletes, scopeRoot/scopeOrdered, data cast, grandchild nesting.
- `GlobalSetModelTest` — translations/origin relationships, findByHandle fallback, cache invalidation, getValue nested dot-notation.
- `BlueprintPolicyTest` — full policy matrix for Admin/Editor/Contributor roles including deleteAny for Collection/Taxonomy/GlobalSet.
- `UserRoleTest` — label/icon/color helpers, SuperAdmin permission bypass, role permission assignments.

---

## [0.5.0] — 2026-03-08

### Added
- `php artisan mipresscz:install` command with `--admin-name`, `--admin-email`, `--admin-password`, `--force`, `--seed` options.
- Installer provisions migrations, roles/permissions, default Collection + Blueprint, locale init, and admin user in a single command.
- `InstallCommandTest` covering full installation flow and idempotent re-run.

---

## [0.4.0] — 2026-03-07

### Added
- Locale routing contract: single-language mode (no prefix) and multi-language mode (locale prefix), with redirect from prefixed to unprefixed in single mode.
- `SetFrontendLocale` middleware updated to handle both modes via `LocaleService`.
- Routing test matrix covering all single/multi locale scenarios.

### Changed
- `routes/web.php` thinned to core includes + app-specific overrides.

---

## [0.3.0] — 2026-03-06

### Added
- Full domain core extracted into `packages/mipresscz/core`:
  - Models: `Collection`, `Blueprint`, `Entry`, `Taxonomy`, `Term`, `GlobalSet`, `Locale`.
  - Enums: `EntryStatus`, `DefaultStatus`, `DateBehavior`.
  - Services: `LocaleService`, URL resolution helpers.
  - Observers: `EntryObserver`.
  - Policies: `CollectionPolicy`, `EntryPolicy`, `TaxonomyPolicy`, `BlockPolicy`, `GlobalSetPolicy`.
  - Filament Resources: Collections, Entries (dynamic `EntryResourceConfiguration`), Taxonomies, Terms, Globals.
  - HTTP: `EntryController` (frontend rendering).
  - Seeders: `RolesAndPermissionsSeeder`, `ContentSeeder`, `GlobalsSeeder`, `LocaleSeeder`.
  - Factories for all content models.
  - Lang `cs`/`en` content translations.
  - Entry template views with fallback.

---

## [0.2.0] — 2026-03-05

### Added
- `MiPressCzAdminPanelProvider` base class in core with hook methods:
  `configureBase()`, `configureNavigation()`, `configurePlugins()`, `configureMiddleware()`, `configureDiscovery()`.
- App `AdminPanelProvider` reduced to a thin extension layer.
- Curator, AuthDesigner, and LanguageSwitch plugins moved to core configuration (overridable in app).

---

## [0.1.0] — 2026-03-04

### Added
- Initial `packages/mipresscz/core` package skeleton with `composer.json`, `MiPressCzCoreServiceProvider` (via `spatie/laravel-package-tools`).
- Publish tags: `mipresscz-config`, `mipresscz-views`, `mipresscz-translations`, `mipresscz-migrations`.
- `helpers.php` with `locales()`, `GlobalSet::findByHandle()` / `GlobalSet::getValue()` delegation.
- Root `composer.json` wired to core via path repository.

---

[Unreleased]: https://github.com/mipresscz/core/compare/v0.6.0...HEAD
[0.6.0]: https://github.com/mipresscz/core/compare/v0.5.0...v0.6.0
[0.5.0]: https://github.com/mipresscz/core/compare/v0.4.0...v0.5.0
[0.4.0]: https://github.com/mipresscz/core/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/mipresscz/core/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/mipresscz/core/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/mipresscz/core/releases/tag/v0.1.0

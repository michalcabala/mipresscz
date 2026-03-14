<?php

namespace MiPressCz\Core\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use MiPressCz\Core\Support\Blink;

/**
 * Provides centralized origin/translation logic for models with
 * `origin_id` + `locale` columns {Entry, Term, GlobalSet}.
 *
 * Requires the using model to have:
 * - `origin_id` (nullable FK to self)
 * - `locale` (string)
 *
 * The trait defines `origin()` and `translations()` relationships
 * and all i18n helper methods, with Blink request-level caching.
 */
trait HasOrigin
{
    // ── Relationships ──

    public function origin(): BelongsTo
    {
        return $this->belongsTo(static::class, 'origin_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(static::class, 'origin_id');
    }

    // ── Origin helpers ──

    public function isOrigin(): bool
    {
        return $this->origin_id === null;
    }

    public function isTranslation(): bool
    {
        return $this->origin_id !== null;
    }

    /**
     * Resolve the origin entry (self if already origin),
     * with Blink request-level cache to prevent N+1.
     */
    public function getOrigin(): ?static
    {
        if ($this->isOrigin()) {
            return $this;
        }

        return $this->blink()->once(
            $this->originBlinkKey(),
            fn () => $this->origin
        );
    }

    /**
     * Walk up the chain to the root origin ancestor.
     */
    public function originRoot(): static
    {
        $entry = $this;

        while ($entry->isTranslation()) {
            $entry = $entry->getOrigin();
        }

        return $entry;
    }

    // ── Translation helpers ──

    /**
     * Find a specific locale variant (self, origin, or sibling).
     */
    public function getTranslation(string $locale): ?static
    {
        if ($this->locale === $locale) {
            return $this;
        }

        $origin = $this->getOrigin();

        if ($origin && $origin->locale === $locale) {
            return $origin;
        }

        return $origin?->translations()->where('locale', $locale)->first();
    }

    /**
     * All locale variants (origin + translations) keyed by locale.
     */
    public function getTranslations(): Collection
    {
        $origin = $this->getOrigin();

        if (! $origin) {
            return collect([$this->locale => $this]);
        }

        return $origin->translations->prepend($origin)->keyBy('locale');
    }

    /**
     * @return list<string>
     */
    public function getAvailableLocales(): array
    {
        return $this->getTranslations()->keys()->all();
    }

    /**
     * @return list<string>
     */
    public function getMissingLocales(): array
    {
        $available = $this->getAvailableLocales();

        return array_values(array_diff(locales()->getActiveCodes(), $available));
    }

    // ── Blink cache ──

    protected function blink(): Blink
    {
        return app(Blink::class);
    }

    protected function originBlinkKey(): string
    {
        return 'origin-'.class_basename(static::class).'-'.$this->getKey();
    }
}

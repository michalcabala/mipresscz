<?php

namespace App\Models;

use App\Enums\EntryStatus;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Str;

class Entry extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'collection_id',
        'blueprint_id',
        'origin_id',
        'locale',
        'title',
        'slug',
        'uri',
        'data',
        'content',
        'status',
        'published_at',
        'expired_at',
        'parent_id',
        'order',
        'author_id',
        'is_pinned',
        'settings',
        'featured_image_id',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'content' => 'array',
            'settings' => 'array',
            'status' => EntryStatus::class,
            'published_at' => 'datetime',
            'expired_at' => 'datetime',
            'is_pinned' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Entry $entry) {
            if (empty($entry->slug)) {
                $entry->slug = static::generateUniqueSlug($entry->title, $entry->collection_id, $entry->locale);
            }
            $entry->uri = $entry->generateUri();
        });

        static::updating(function (Entry $entry) {
            if ($entry->isDirty('slug') || $entry->isDirty('title')) {
                if ($entry->isDirty('title') && ! $entry->isDirty('slug')) {
                    $entry->slug = static::generateUniqueSlug($entry->title, $entry->collection_id, $entry->locale, $entry->id);
                }
                $entry->uri = $entry->generateUri();
            }
        });
    }

    // ── Relationships ──

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_image_id');
    }

    public function blueprint(): BelongsTo
    {
        return $this->belongsTo(Blueprint::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Entry::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Entry::class, 'parent_id');
    }

    public function origin(): BelongsTo
    {
        return $this->belongsTo(Entry::class, 'origin_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(Entry::class, 'origin_id');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(Revision::class);
    }

    public function terms(): MorphToMany
    {
        return $this->morphToMany(Term::class, 'termable', 'termables')
            ->withPivot('order')
            ->orderBy('termables.order');
    }

    public function relatedEntries(string $fieldHandle): BelongsToMany
    {
        return $this->belongsToMany(Entry::class, 'entry_relationships', 'parent_entry_id', 'related_entry_id')
            ->wherePivot('field_handle', $fieldHandle)
            ->withPivot(['field_handle', 'order'])
            ->orderBy('entry_relationships.order');
    }

    public function referencedBy(): BelongsToMany
    {
        return $this->belongsToMany(Entry::class, 'entry_relationships', 'related_entry_id', 'parent_entry_id')
            ->withPivot(['field_handle', 'order']);
    }

    // ── Scopes ──

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', EntryStatus::Published)
            ->where('published_at', '<=', now())
            ->where(function (Builder $q) {
                $q->whereNull('expired_at')->orWhere('expired_at', '>', now());
            });
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', EntryStatus::Draft);
    }

    public function scopeLocale(Builder $query, string $locale): Builder
    {
        return $query->where('locale', $locale);
    }

    public function scopeInCollection(Builder $query, string $handle): Builder
    {
        return $query->whereHas('collection', fn (Builder $q) => $q->where('handle', $handle));
    }

    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy(
            $this->collection?->sort_field ?? 'order',
            $this->collection?->sort_direction ?? 'asc'
        );
    }

    // ── Multijazyčnost ──

    public function isOrigin(): bool
    {
        return $this->origin_id === null;
    }

    public function isTranslation(): bool
    {
        return $this->origin_id !== null;
    }

    public function getOrigin(): ?Entry
    {
        return $this->isOrigin() ? $this : $this->origin;
    }

    public function getTranslation(string $locale): ?Entry
    {
        if ($this->locale === $locale) {
            return $this;
        }

        $origin = $this->getOrigin();

        if ($origin->locale === $locale) {
            return $origin;
        }

        return $origin->translations()->where('locale', $locale)->first();
    }

    public function getTranslations(): BaseCollection
    {
        $origin = $this->getOrigin();

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

    /**
     * @return array<string, mixed>
     */
    public function getTranslatableData(): array
    {
        $handles = collect($this->blueprint?->getTranslatableFields() ?? [])
            ->pluck('handle')
            ->all();

        return collect($this->data ?? [])
            ->only($handles)
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function getNonTranslatableData(): array
    {
        $handles = collect($this->blueprint?->getNonTranslatableFields() ?? [])
            ->pluck('handle')
            ->all();

        return collect($this->data ?? [])
            ->only($handles)
            ->all();
    }

    public function inheritFromOrigin(): void
    {
        if (! $this->isTranslation()) {
            return;
        }

        $origin = $this->getOrigin();
        $nonTranslatable = $origin->getNonTranslatableData();

        $this->data = array_merge($this->data ?? [], $nonTranslatable);
    }

    // ── URL & SEO ──

    public function getFullUrl(): ?string
    {
        if (! $this->uri) {
            return null;
        }

        $locale = locales()->findByCode($this->locale);
        $prefix = $locale?->url_prefix ? '/'.$locale->url_prefix : '';

        return url($prefix.$this->uri);
    }

    /**
     * @return array<string, string>
     */
    public function getHreflangTags(): array
    {
        $translations = $this->getTranslations();
        $tags = [];

        foreach ($translations as $localeCode => $entry) {
            $url = $entry->getFullUrl();
            if ($url) {
                $tags[$localeCode] = $url;
            }
        }

        $defaultLocale = locales()->getDefault();
        if ($defaultLocale && isset($tags[$defaultLocale->code])) {
            $tags['x-default'] = $tags[$defaultLocale->code];
        }

        return $tags;
    }

    // ── Slug & URI ──

    public static function generateUniqueSlug(string $title, string $collectionId, string $locale, ?string $excludeId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 2;

        while (
            static::query()
                ->where('collection_id', $collectionId)
                ->where('locale', $locale)
                ->where('slug', $slug)
                ->when($excludeId, fn (Builder $q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = $originalSlug.'-'.$counter++;
        }

        return $slug;
    }

    public function generateUri(): ?string
    {
        $template = $this->collection?->route_template;

        if (! $template) {
            return null;
        }

        $replacements = [
            '{slug}' => $this->slug,
            '{year}' => $this->published_at?->format('Y') ?? now()->format('Y'),
            '{month}' => $this->published_at?->format('m') ?? now()->format('m'),
            '{day}' => $this->published_at?->format('d') ?? now()->format('d'),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }
}

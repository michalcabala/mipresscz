<?php

declare(strict_types=1);

namespace MiPressCz\Core\Models;

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
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use MiPressCz\Core\Concerns\ContainsComputedData;
use MiPressCz\Core\Concerns\HasNavMenuItems;
use MiPressCz\Core\Concerns\HasOrigin;
use MiPressCz\Core\Concerns\HasRevisions;
use MiPressCz\Core\Enums\EntryStatus;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

class Entry extends Model implements Feedable
{
    use ContainsComputedData;
    use HasFactory;
    use HasNavMenuItems;
    use HasOrigin;
    use HasRevisions;
    use HasUlids;
    use Searchable;
    use SoftDeletes;

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
        'is_homepage',
        'settings',
        'featured_image_id',
        'meta_title',
        'meta_description',
        'meta_og_image_id',
        'preview_token',
        'preview_token_expires_at',
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
            'is_homepage' => 'boolean',
            'preview_token_expires_at' => 'datetime',
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

    public function metaOgImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'meta_og_image_id');
    }

    public function blueprint(): BelongsTo
    {
        return $this->belongsTo(Blueprint::class);
    }

    /** @phpstan-ignore-next-line */
    public function author(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', \App\Models\User::class), 'author_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Entry::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Entry::class, 'parent_id');
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

    // ── Blueprint data helpers ──

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

    public function getMenuLabel(): string
    {
        return $this->title ?? '';
    }

    public function getMenuUrl(): string
    {
        return $this->getFullUrl() ?? '';
    }

    public function getMenuIcon(): ?string
    {
        return 'heroicon-o-document-text';
    }

    public function getFullUrl(): ?string
    {
        if (! $this->uri) {
            return null;
        }

        $prefix = '';

        if (locales()->shouldPrefixUrls()) {
            $locale = locales()->findByCode($this->locale);
            $prefix = $locale?->url_prefix ? '/'.$locale->url_prefix : '';
        }

        return url($prefix.$this->uri);
    }

    public function getSitemapUrl(): ?string
    {
        if (! $this->isSitemapEligible()) {
            return null;
        }

        return $this->getFullUrl();
    }

    public static function getFeedItems(): \Illuminate\Database\Eloquent\Collection
    {
        $locale = app()->getLocale();

        return static::query()
            ->with('collection')
            ->published()
            ->whereHas('collection')
            ->where('locale', $locale)
            ->orderByDesc('published_at')
            ->limit(50)
            ->get();
    }

    public function toFeedItem(): FeedItem
    {
        $url = $this->getFullUrl() ?? url('/');

        return FeedItem::create()
            ->id($url)
            ->title($this->title ?? '')
            ->summary($this->meta_description ?? '')
            ->updated($this->published_at ?? $this->updated_at ?? now())
            ->link($url)
            ->authorName(config('app.name'));
    }

    private function isSitemapEligible(): bool
    {
        if ($this->status !== EntryStatus::Published) {
            return false;
        }

        if ($this->published_at?->isFuture()) {
            return false;
        }

        if ($this->expired_at?->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * @return array<string, string>
     */
    public function getHreflangTags(): array
    {
        if (! locales()->shouldPrefixUrls()) {
            return [];
        }

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

    // ── Search ──

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'locale' => $this->locale,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
        ];
    }

    public function shouldBeSearchable(): bool
    {
        return $this->status === EntryStatus::Published
            && $this->published_at?->isPast();
    }

    // ── Preview ──

    public function generatePreviewToken(): string
    {
        $this->preview_token = Str::random(64);
        $this->preview_token_expires_at = now()->addHours(24);
        $this->saveQuietly();

        return $this->preview_token;
    }

    public function isPreviewTokenValid(string $token): bool
    {
        return $this->preview_token === $token
            && $this->preview_token_expires_at
            && $this->preview_token_expires_at->isFuture();
    }

    public function getPreviewUrl(): string
    {
        if (! $this->preview_token || ! $this->preview_token_expires_at?->isFuture()) {
            $this->generatePreviewToken();
        }

        return url('_preview/'.$this->preview_token);
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

    // ── Content helpers ──

    /**
     * Extract plain text from Mason content blocks.
     * Walks through all bricks, strips HTML tags, and returns concatenated text.
     */
    public function getPlainTextContent(): string
    {
        $blocks = $this->content;

        if (! is_array($blocks) || $blocks === []) {
            return '';
        }

        $texts = [];

        foreach ($blocks as $block) {
            $config = $block['attrs']['config'] ?? [];
            $this->extractTextFromConfig($config, $texts);
        }

        return implode(' ', array_filter($texts));
    }

    /**
     * Recursively extract text values from a Mason brick config.
     *
     * @param  array<string, mixed>  $config
     * @param  array<int, string>  $texts
     */
    private function extractTextFromConfig(array $config, array &$texts): void
    {
        foreach ($config as $key => $value) {
            if (is_string($value) && in_array($key, ['content', 'text', 'heading', 'subheading', 'description', 'eyebrow', 'title', 'label', 'value'], true)) {
                $plain = trim(strip_tags($value));
                if ($plain !== '') {
                    $texts[] = $plain;
                }
            } elseif ($key === 'items' && is_array($value)) {
                foreach ($value as $item) {
                    if (is_array($item)) {
                        $this->extractTextFromConfig($item, $texts);
                    }
                }
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function toRevisionSnapshot(): array
    {
        return [
            'collection_id' => $this->collection_id,
            'blueprint_id' => $this->blueprint_id,
            'origin_id' => $this->origin_id,
            'locale' => $this->locale,
            'title' => $this->title,
            'slug' => $this->slug,
            'uri' => $this->uri,
            'data' => $this->data ?? [],
            'content' => $this->content,
            'status' => $this->status instanceof EntryStatus ? $this->status->value : $this->status,
            'published_at' => $this->published_at?->toISOString(),
            'expired_at' => $this->expired_at?->toISOString(),
            'parent_id' => $this->parent_id,
            'order' => $this->order,
            'author_id' => $this->author_id,
            'is_pinned' => $this->is_pinned ?? false,
            'is_homepage' => $this->is_homepage ?? false,
            'settings' => $this->settings,
            'featured_image_id' => $this->featured_image_id,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_og_image_id' => $this->meta_og_image_id,
        ];
    }
}

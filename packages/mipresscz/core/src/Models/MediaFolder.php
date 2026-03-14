<?php

namespace MiPressCz\Core\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MiPressCz\Core\Database\Factories\MediaFolderFactory;
use Openplain\FilamentTreeView\Concerns\HasTreeStructure;

class MediaFolder extends Model
{
    use HasFactory, HasTreeStructure;

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'order',
    ];

    protected static function newFactory(): MediaFolderFactory
    {
        return MediaFolderFactory::new();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('order');
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class, 'media_folder_id');
    }
}

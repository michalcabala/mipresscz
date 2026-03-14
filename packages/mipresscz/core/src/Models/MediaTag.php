<?php

namespace MiPressCz\Core\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use MiPressCz\Core\Database\Factories\MediaTagFactory;

class MediaTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function newFactory(): MediaTagFactory
    {
        return MediaTagFactory::new();
    }

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'media_media_tag');
    }
}

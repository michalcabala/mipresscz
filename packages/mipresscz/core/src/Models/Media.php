<?php

namespace MiPressCz\Core\Models;

use Awcodes\Curator\Models\Media as CuratorMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Media extends CuratorMedia
{
    protected $table = 'curator';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->fillable = array_merge($this->fillable, ['media_folder_id']);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'media_folder_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(MediaTag::class, 'media_media_tag', 'media_id', 'media_tag_id');
    }
}

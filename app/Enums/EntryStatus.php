<?php

namespace App\Enums;

enum EntryStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Scheduled = 'scheduled';
    case Archived = 'archived';

    public function label(): string
    {
        return __('content.statuses.'.$this->value);
    }

    public function icon(): string
    {
        return match ($this) {
            self::Draft => 'fal-pen',
            self::Published => 'fal-circle-check',
            self::Scheduled => 'fal-clock',
            self::Archived => 'fal-box-archive',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Published => 'success',
            self::Scheduled => 'info',
            self::Archived => 'warning',
        };
    }
}

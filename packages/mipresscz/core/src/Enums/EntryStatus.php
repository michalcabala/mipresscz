<?php

namespace MiPressCz\Core\Enums;

use Filament\Support\Contracts\HasLabel;

enum EntryStatus: string implements HasLabel
{
    case Draft = 'draft';
    case Published = 'published';
    case Scheduled = 'scheduled';
    case Archived = 'archived';

    public function getLabel(): string
    {
        return __('content.statuses.'.$this->value);
    }

    public function label(): string
    {
        return $this->getLabel();
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

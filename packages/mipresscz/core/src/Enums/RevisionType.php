<?php

declare(strict_types=1);

namespace MiPressCz\Core\Enums;

enum RevisionType: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Autosave = 'autosave';
    case Rollback = 'rollback';

    public function label(): string
    {
        return match ($this) {
            self::Draft => __('revisions.types.draft'),
            self::Published => __('revisions.types.published'),
            self::Autosave => __('revisions.types.autosave'),
            self::Rollback => __('revisions.types.rollback'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'info',
            self::Published => 'success',
            self::Autosave => 'gray',
            self::Rollback => 'warning',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Draft => 'heroicon-o-pencil-square',
            self::Published => 'heroicon-o-check-circle',
            self::Autosave => 'heroicon-o-clock',
            self::Rollback => 'heroicon-o-arrow-uturn-left',
        };
    }
}

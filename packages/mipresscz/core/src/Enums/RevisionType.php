<?php

declare(strict_types=1);

namespace MiPressCz\Core\Enums;

enum RevisionType: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Autosave = 'autosave';
    case Rollback = 'rollback';
}

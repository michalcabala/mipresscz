<?php

namespace MiPressCz\Core\Enums;

enum DefaultStatus: string
{
    case Draft = 'draft';
    case Published = 'published';

    public function label(): string
    {
        return __('content.statuses.'.$this->value);
    }
}

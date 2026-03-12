<?php

namespace MiPressCz\Core\Enums;

enum MenuItemTarget: string
{
    case Self = '_self';
    case Blank = '_blank';

    public function label(): string
    {
        return __('content.menu_item_targets.'.$this->value);
    }
}

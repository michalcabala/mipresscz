<?php

namespace MiPressCz\Core\Enums;

enum MenuItemType: string
{
    case CustomLink = 'custom_link';
    case Entry = 'entry';

    public function label(): string
    {
        return __('content.menu_item_types.'.$this->value);
    }

    public function icon(): string
    {
        return match ($this) {
            self::CustomLink => 'fal-link',
            self::Entry => 'fal-file-lines',
        };
    }
}

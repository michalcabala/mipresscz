<?php

namespace MiPressCz\Core\Enums;

enum DateBehavior: string
{
    case None = 'none';
    case Required = 'required';
    case Optional = 'optional';

    public function label(): string
    {
        return __('content.date_behaviors.'.$this->value);
    }
}

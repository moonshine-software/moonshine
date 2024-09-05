<?php

declare(strict_types=1);

namespace MoonShine\Support\Enums;

enum FormMethod: string
{
    case POST = 'post';

    case GET = 'get';

    public function toString(): string
    {
        return $this->value;
    }
}

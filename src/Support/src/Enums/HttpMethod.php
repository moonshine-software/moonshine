<?php

declare(strict_types=1);

namespace MoonShine\Support\Enums;

enum HttpMethod: string
{
    case POST = 'post';

    case GET = 'get';

    case HEAD = 'head';

    case PUT = 'put';

    case PATCH = 'patch';

    case DELETE = 'delete';

    case OPTIONS = 'options';

    case TRACE = 'trace';

    case CONNECT = 'connect';

    public function toString(): string
    {
        return $this->value;
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\Laravel\DependencyInjection;

use MoonShine\Core\AbstractRequest;

final class Request extends AbstractRequest
{
    public function getSession(string $key, mixed $default = null): mixed
    {
        return session($key, $default);
    }

    public function getFormErrors(?string $bag = null): array
    {
        return $this->getSession('errors')
            ?->{$bag}
            ?->toArray() ?? [];
    }

    public function getFile(string $key): mixed
    {
        return request()->file($key);
    }

    public function getOld(string $key, mixed $default = null): mixed
    {
        return old($key, $default);
    }
}

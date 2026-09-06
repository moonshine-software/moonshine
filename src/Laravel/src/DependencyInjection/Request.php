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
        /** @var \Illuminate\Support\ViewErrorBag|null $errors */
        $errors = $this->getSession('errors');

        /** @var array<string, mixed> $messages */
        $messages = $errors?->getBag($bag ?? 'default')->getMessages() ?? [];

        return $messages;
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

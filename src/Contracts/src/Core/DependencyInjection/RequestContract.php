<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

use Illuminate\Support\Collection;
use Psr\Http\Message\ServerRequestInterface;

interface RequestContract
{
    public function getRequest(): ServerRequestInterface;

    public function get(string $key, mixed $default = null): mixed;

    public function has(string $key): bool;

    public function getAll(): Collection;

    public function getSession(string $key, mixed $default = null): mixed;

    public function getFormErrors(?string $bag = null): array;

    public function getFile(string $key): mixed;

    public function getOld(string $key, mixed $default = null): mixed;

    public function getOnly(array|string $keys): array;

    public function getExcept(array|string $keys): array;

    public function getHost(): string;

    public function getPath(): string;

    public function getUrl(): string;

    public function urlIs(...$patterns): bool;

    public function getUrlWithQuery(array $query): string;
}

<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

use Illuminate\Support\Collection;
use Psr\Http\Message\ServerRequestInterface;

interface RequestContract
{
    public function getRequest(): ServerRequestInterface;

    public function get(string $key, mixed $default = null): mixed;

    public function getScalar(string $key, mixed $default = null): mixed;

    public function has(string $key): bool;

    /**
     * @return Collection<string, mixed>
     */
    public function getAll(): Collection;

    public function getSession(string $key, mixed $default = null): mixed;

    /**
     * @return array<string, mixed>
     */
    public function getFormErrors(?string $bag = null): array;

    public function getFile(string $key): mixed;

    public function getOld(string $key, mixed $default = null): mixed;

    /**
     * @param  array<string, mixed>|string  $keys
     *
     * @return array<string, mixed>
     */
    public function getOnly(array|string $keys): array;

    /**
     * @param  array<string, mixed>|string  $keys
     *
     * @return array<string, mixed>
     */
    public function getExcept(array|string $keys): array;

    public function getHost(): string;

    public function getPath(): string;

    public function getUrl(): string;

    /**
     * @param  string[] $patterns
     */
    public function urlIs(...$patterns): bool;

    /**
     * @param  array<string, string|int|float|null> $query
     */
    public function getUrlWithQuery(array $query): string;

    public function isAjax(): bool;

    public function isWantsJson(): bool;
}

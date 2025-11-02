<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use Illuminate\Support\Collection;

/**
 * @internal
 */
interface CrudResourceWithQueryParamsContract
{
    /**
     * @param  iterable<string, mixed>  $params
     */
    public function setQueryParams(iterable $params): static;

    /**
     * @return  array<string, mixed>
     */
    public function getQueryParamsKeys(): array;

    /**
     * @return  Collection<string, mixed>
     */
    public function getQueryParams(): Collection;

    public function getQueryParamPrefix(): string;

    public function getQueryParamName(string $key): string;

    public function getQueryParam(string $key, mixed $default = null): mixed;

    public function hasQueryParam(string $key): bool;
}

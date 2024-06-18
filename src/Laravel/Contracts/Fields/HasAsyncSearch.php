<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Contracts\Fields;

use Closure;

interface HasAsyncSearch
{
    public function isAsyncSearch(): bool;

    public function getAsyncSearchColumn(): ?string;

    public function getAsyncSearchQuery(): ?Closure;

    public function getAsyncSearchCount(): int;

    public function getAsyncSearchValueCallback(): ?Closure;

    public function asyncSearch(
        string $column = null,
        ?Closure $searchQuery = null,
        ?Closure $formatted = null,
        ?string $associatedWith = null,
        int $limit = 15,
        ?string $url = null,
    ): static;

}

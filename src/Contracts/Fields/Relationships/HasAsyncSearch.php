<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Fields\Relationships;

use Closure;

interface HasAsyncSearch
{
    public function isAsyncSearch(): bool;

    public function asyncSearchColumn(): ?string;

    public function asyncSearchQuery(): ?Closure;

    public function asyncSearchCount(): int;

    public function asyncSearchValueCallback(): ?Closure;

    public function asyncSearch(
        string $column = null,
        ?Closure $searchQuery = null,
        ?Closure $formatted = null,
        ?string $associatedWith = null,
        int $limit = 15,
        ?string $url = null,
    ): static;

}

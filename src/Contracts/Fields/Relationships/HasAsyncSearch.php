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
        string $asyncSearchColumn = null,
        int $asyncSearchCount = 15,
        ?Closure $asyncSearchQuery = null,
        ?Closure $asyncSearchValueCallback = null
    ): static;

}

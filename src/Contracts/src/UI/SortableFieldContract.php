<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;

interface SortableFieldContract
{
    public function sortable(Closure|string|null $callback = null): static;

    public function disableSortable(): static;

    public function getSortableCallback(): Closure|string|null;

    public function isSortable(): bool;

    public function getSortQuery(?string $url = null): string;

    public function isSortActive(): bool;

    public function sortDirectionIs(string $type): bool;
}

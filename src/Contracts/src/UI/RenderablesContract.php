<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;
use Traversable;

interface RenderablesContract extends Traversable
{
    public function exceptElements(Closure $except): static;

    public function toStructure(bool $withStates = true): array;
}

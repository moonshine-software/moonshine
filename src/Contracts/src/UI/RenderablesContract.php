<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;
use MoonShine\Contracts\Core\RenderableContract;
use Traversable;

/**
 * @template-implements Traversable<array-key, RenderableContract>
 */
interface RenderablesContract extends Traversable
{
    public function exceptElements(Closure $except): static;

    public function toStructure(bool $withStates = true): array;
}

<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;

/**
 * @template-covariant T of ComponentContract
 */
interface HasOffCanvasContract
{
    /**
     * @return ?T
     */
    public function getOffCanvas(): ?ComponentContract;

    public function isInOffCanvas(): bool;

    /**
     * @param  list<ComponentContract>  $components
     */
    public function inOffCanvas(
        Closure|string|null $title = null,
        Closure|string|null $content = null,
        Closure|string|null $name = null,
        ?Closure $builder = null,
        iterable $components = [],
    ): static;

    public function toggleOffCanvas(string $name = 'default'): static;

    public function openOffCanvas(): static;
}

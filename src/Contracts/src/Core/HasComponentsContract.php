<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use MoonShine\Contracts\UI\Collection\ComponentsContract;
use MoonShine\Contracts\UI\ComponentContract;

interface HasComponentsContract
{
    /**
     * @param  iterable<array-key, ComponentContract>  $components
     *
     */
    public function setComponents(iterable $components): static;

    public function hasComponents(): bool;

    public function getComponents(): ComponentsContract;
}

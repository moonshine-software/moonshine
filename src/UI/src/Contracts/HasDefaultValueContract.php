<?php

declare(strict_types=1);

namespace MoonShine\UI\Contracts;

interface HasDefaultValueContract
{
    public function default(mixed $default): static;

    public function getDefault(): mixed;
}

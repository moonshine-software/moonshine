<?php

declare(strict_types=1);

namespace MoonShine\UI\Contracts\Fields;

interface HasDefaultValue
{
    public function default(mixed $default): static;

    public function getDefault(): mixed;
}

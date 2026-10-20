<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;

interface HasLabelContract
{
    public function hasLabel(): bool;

    public function getLabel(): string;

    public function setLabel(Closure|string $label): static;

    public function translatable(string $key = ''): static;
}

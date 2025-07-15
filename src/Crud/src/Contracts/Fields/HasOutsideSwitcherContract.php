<?php

declare(strict_types=1);

namespace MoonShine\Crud\Contracts\Fields;

interface HasOutsideSwitcherContract
{
    public function isOutsideComponent(): bool;
}

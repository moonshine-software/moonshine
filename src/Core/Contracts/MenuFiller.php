<?php

declare(strict_types=1);

namespace MoonShine\Core\Contracts;

interface MenuFiller
{
    public function url(): string;

    public function isActive(): bool;
}

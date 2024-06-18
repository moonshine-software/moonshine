<?php

declare(strict_types=1);

namespace MoonShine\Core\Contracts;

interface MenuFiller
{
    public function getUrl(): string;

    public function isActive(): bool;
}

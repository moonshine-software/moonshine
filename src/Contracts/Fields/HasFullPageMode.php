<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts\Fields;

interface HasFullPageMode
{
    public function fullPage(): static;

    public function isFullPage(): bool;
}

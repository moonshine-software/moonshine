<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts\Fields;

interface HasAssets
{
    public function getAssets(): array;
}

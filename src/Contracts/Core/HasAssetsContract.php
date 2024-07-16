<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

interface HasAssetsContract
{
    public function getAssets(): array;
}

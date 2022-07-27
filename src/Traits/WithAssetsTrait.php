<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits;

trait WithAssetsTrait
{
    protected array $assets = [];

    public function getAssets(): array
    {
        return $this->assets;
    }
}

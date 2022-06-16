<?php

namespace Leeto\MoonShine\Traits;

trait WithAssetsTrait
{
    protected array $assets = [];

    public function getAssets(): array
    {
        return $this->assets;
    }
}
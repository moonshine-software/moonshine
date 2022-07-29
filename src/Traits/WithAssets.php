<?php

namespace Leeto\MoonShine\Traits;

trait WithAssets
{
    protected array $assets = [];

    public function getAssets(): array
    {
        return $this->assets;
    }
}

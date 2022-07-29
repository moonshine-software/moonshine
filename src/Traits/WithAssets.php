<?php

namespace Leeto\MoonShine\Traits;

trait WithAssets
{
    /**
     * @var array<string>
     */
    protected array $assets = [];

    public function getAssets(): array
    {
        return $this->assets;
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\Support\Traits;

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

    public function addAssets(array $assets): static
    {
        moonshineAssets()->add($assets);

        return $this;
    }
}

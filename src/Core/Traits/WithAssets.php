<?php

declare(strict_types=1);

namespace MoonShine\Core\Traits;

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
        $this->assetManager->add($assets);

        return $this;
    }
}

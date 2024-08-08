<?php

declare(strict_types=1);

namespace MoonShine\Core\Traits;

use MoonShine\Contracts\AssetManager\AssetManagerContract;

/**
 * @mixin WithCore
 */
trait WithAssets
{
    /**
     * @var array<string>
     */
    protected array $assets = [];

    public function getAssetManager(): AssetManagerContract
    {
        return $this->getCore()->getContainer(AssetManagerContract::class);
    }

    public function getAssets(): array
    {
        return $this->assets;
    }

    public function addAssets(array $assets): static
    {
        $this->getAssetManager()->add($assets);

        return $this;
    }

    public function pushAssets(array $assets): static
    {
        $this->assets = array_merge($this->assets, $assets);

        return $this;
    }
}

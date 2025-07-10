<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use MoonShine\Contracts\AssetManager\AssetElementContract;
use MoonShine\Contracts\AssetManager\AssetManagerContract;

interface HasAssetsContract
{
    /**
     * @return list<AssetElementContract>
     */
    public function getAssets(): array;

    public function getAssetManager(): AssetManagerContract;

    /**
     * @param list<AssetElementContract> $assets
     */
    public function addAssets(array $assets): static;

    /**
     * @param list<AssetElementContract> $assets
     */
    public function pushAssets(array $assets): static;
}

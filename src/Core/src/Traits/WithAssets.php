<?php

declare(strict_types=1);

namespace MoonShine\Core\Traits;

use MoonShine\Contracts\AssetManager\AssetElementContract;
use MoonShine\Contracts\AssetManager\AssetManagerContract;

/**
 * @mixin WithCore
 */
trait WithAssets
{
    /**
     * @var list<AssetElementContract>
     */
    protected array $assets = [];

    public function getAssetManager(): AssetManagerContract
    {
        return $this->getCore()->getContainer(AssetManagerContract::class);
    }

    /**
     * @return list<AssetElementContract>
     */
    protected function assets(): array
    {
        return [];
    }

    /**
     * @return list<AssetElementContract>
     */
    public function getAssets(): array
    {
        if (! $this->shouldUseAssets()) {
            return [];
        }

        return array_merge(
            $this->assets,
            $this->assets(),
        );
    }

    protected function shouldUseAssets(): bool
    {
        return true;
    }

    /**
     * @param list<AssetElementContract> $assets
     */
    public function addAssets(array $assets): static
    {
        $this->getAssetManager()->add($assets);

        return $this;
    }

    /**
     * @param list<AssetElementContract> $assets
     */
    public function pushAssets(array $assets): static
    {
        $this->assets = array_merge($this->assets, $assets);

        return $this;
    }
}

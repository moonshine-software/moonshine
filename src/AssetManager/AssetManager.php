<?php

declare(strict_types=1);

namespace MoonShine\AssetManager;

use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\AssetManager\AssetElementContract;
use MoonShine\Contracts\AssetManager\AssetElementsContract;
use MoonShine\Contracts\AssetManager\AssetManagerContract;
use MoonShine\Contracts\AssetManager\AssetResolverContract;

final class AssetManager implements AssetManagerContract
{
    use Conditionable;

    public function __construct(
        private readonly AssetResolverContract $assetResolver
    ) {
    }

    /**
     * @var list<AssetElementContract>
     */
    private array $assets = [];

    public function getAsset(string $path): string
    {
        return $this->assetResolver->get($path);
    }

    public function getViteDev(string $path): string
    {
        return $this->assetResolver->getDev($path);
    }

    /**
     * @param  list<AssetElementContract> $assets
     */
    public function add(AssetElementContract|array $assets): static
    {
        $this->assets = array_unique(
            array_merge(
                $this->assets,
                is_array($assets) ? $assets : [$assets]
            )
        );

        return $this;
    }

    public function getAssets(): AssetElementsContract
    {
        return AssetElements::make($this->assets);
    }

    public function toHtml(): string
    {
        return $this->getAssets()
            ->ensure(AssetElementContract::class)
            ->when(
                $this->isRunningHot(),
                fn (AssetElementsContract $assets) => $assets
                    ->push(
                        Raw::make($this->getViteDev($this->getHotFile()))
                    ),
            )
            ->resolveLinks($this->assetResolver)
            ->withVersion($this->getVersion())
            ->toHtml();
    }

    private function isRunningHot(): bool
    {
        return $this->assetResolver->isDev() && is_file($this->getHotFile());
    }

    private function getHotFile(): string
    {
        return $this->assetResolver->getHotFile();
    }

    private function getVersion(): string
    {
        return $this->assetResolver->getVersion();
    }
}

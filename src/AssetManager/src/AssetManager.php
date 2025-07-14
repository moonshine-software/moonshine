<?php

declare(strict_types=1);

namespace MoonShine\AssetManager;

use Closure;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\AssetManager\AssetElementContract;
use MoonShine\Contracts\AssetManager\AssetElementsContract;
use MoonShine\Contracts\AssetManager\AssetManagerContract;
use MoonShine\Contracts\AssetManager\AssetResolverContract;

final class AssetManager implements AssetManagerContract
{
    use Conditionable;

    /**
     * @var list<AssetElementContract>
     */
    private array $assets = [];

    /**
     * @var array<AssetElementContract>
     */
    private array $prependedAssets = [];

    /**
     * @var array<AssetElementContract>
     */
    private array $appendedAssets = [];

    /** @var array<Closure(array<AssetElementContract>):array<AssetElementContract>> */
    private array $assetsModifiers = [];

    public function __construct(
        private readonly AssetResolverContract $assetResolver
    ) {
    }

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
        $this->assets = array_values(
            array_unique(
                array_merge(
                    $this->assets,
                    \is_array($assets) ? $assets : [$assets]
                )
            )
        );

        return $this;
    }

    /**
     * @param  list<AssetElementContract> $assets
     */
    public function prepend(AssetElementContract|array $assets): static
    {
        $this->prependedAssets = array_unique(
            array_merge(
                \is_array($assets) ? $assets : [$assets],
                $this->prependedAssets,
            )
        );

        return $this;
    }

    /**
     * @param  list<AssetElementContract> $assets
     */
    public function append(AssetElementContract|array $assets): static
    {
        $this->appendedAssets = array_unique(
            array_merge(
                $this->appendedAssets,
                \is_array($assets) ? $assets : [$assets]
            )
        );

        return $this;
    }

    /**
     * @param Closure(array<AssetElementContract> $assets): array<AssetElementContract> $callback
     */
    public function modifyAssets(Closure $callback): static
    {
        $this->assetsModifiers[] = $callback;

        return $this;
    }

    public function getAssets(): AssetElementsContract
    {
        $assets = $this->assets;

        if ($this->prependedAssets !== []) {
            $assets = [
                ...$this->prependedAssets,
                ...$assets,
            ];
        }

        if ($this->appendedAssets !== []) {
            $assets = [
                ...$assets,
                ...$this->appendedAssets,
            ];
        }

        foreach ($this->assetsModifiers as $assetsModifier) {
            $assets = $assetsModifier($assets);
        }

        return AssetElements::make($assets);
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

    public function isRunningHot(): bool
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

    public function flushState(): void
    {
        $this->assets = [];
        $this->assetsModifiers = [];
        $this->prependedAssets = [];
        $this->appendedAssets = [];
    }
}

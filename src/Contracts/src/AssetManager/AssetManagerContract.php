<?php

declare(strict_types=1);

namespace MoonShine\Contracts\AssetManager;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use MoonShine\Contracts\Core\StatefulContract;

interface AssetManagerContract extends Htmlable, StatefulContract
{
    public function getAsset(string $path): string;

    public function getAssets(): AssetElementsContract;

    /** @param Closure(array<AssetElementContract> $assets): array<AssetElementContract> $callback */
    public function modifyAssets(Closure $callback): static;

    /**
     * @param  list<AssetElementContract> $assets
     */
    public function add(AssetElementContract|array $assets): static;

    /**
     * @param  list<AssetElementContract> $assets
     */
    public function prepend(AssetElementContract|array $assets): static;

    /**
     * @param  list<AssetElementContract> $assets
     */
    public function append(AssetElementContract|array $assets): static;

    public function isRunningHot(): bool;
}

<?php

declare(strict_types=1);

namespace MoonShine\Contracts\AssetManager;

use Illuminate\Contracts\Support\Htmlable;

interface AssetManagerContract extends Htmlable
{
    public function getAsset(string $path): string;

    public function getAssets(): AssetElementsContract;

    public function add(AssetElementContract|array $assets): static;
}

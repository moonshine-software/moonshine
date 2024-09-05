<?php

declare(strict_types=1);

namespace MoonShine\Contracts\AssetManager;

interface AssetResolverContract
{
    public function get(string $path): string;

    public function getDev(string $path): ?string;

    public function isDev(): bool;

    public function getVersion(): string;

    public function getHotFile(): string;
}

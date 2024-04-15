<?php

declare(strict_types=1);

namespace MoonShine\AssetManager;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;

/**
 * @extends Collection<int, AssetElement>
 */
final class AssetElements extends Collection implements Htmlable
{
    public function js(): self
    {
        return $this->filter(
            fn (AssetElement $asset): int|bool => $asset instanceof Js
        );
    }

    public function css(): self
    {
        return $this->filter(
            fn (AssetElement $asset): int|bool => $asset instanceof Css
        );
    }

    public function inlineCss(): self
    {
        return $this->filter(
            fn (AssetElement $asset): int|bool => $asset instanceof InlineCss
        );
    }

    public function inlineJs(): self
    {
        return $this->filter(
            fn (AssetElement $asset): int|bool => $asset instanceof InlineJs
        );
    }

    public function withVersion(int|string $version): self
    {
        return $this->map(
            fn(AssetElement $asset) => $asset instanceof HasVersion
                ? $asset->version($version)
                : $asset
        );
    }

    public function toHtml(): string
    {
        return $this->implode(
            fn (AssetElement $asset) => $asset
                ->toHtml(),
            PHP_EOL
        );
    }
}

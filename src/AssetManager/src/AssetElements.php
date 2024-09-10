<?php

declare(strict_types=1);

namespace MoonShine\AssetManager;

use Illuminate\Support\Collection;
use MoonShine\AssetManager\Contracts\HasLinkContact;
use MoonShine\AssetManager\Contracts\HasVersionContact;
use MoonShine\Contracts\AssetManager\AssetElementContract;
use MoonShine\Contracts\AssetManager\AssetElementsContract;
use MoonShine\Contracts\AssetManager\AssetResolverContract;

/**
 * @extends Collection<array-key, AssetElementContract>
 */
final class AssetElements extends Collection implements AssetElementsContract
{
    public function js(): self
    {
        return $this->filter(
            static fn (AssetElementContract $asset): int|bool => $asset instanceof Js
        );
    }

    public function css(): self
    {
        return $this->filter(
            static fn (AssetElementContract $asset): int|bool => $asset instanceof Css
        );
    }

    public function inlineCss(): self
    {
        return $this->filter(
            static fn (AssetElementContract $asset): int|bool => $asset instanceof InlineCss
        );
    }

    public function inlineJs(): self
    {
        return $this->filter(
            static fn (AssetElementContract $asset): int|bool => $asset instanceof InlineJs
        );
    }

    public function resolveLinks(AssetResolverContract $resolver): self
    {
        return $this->map(
            static fn (AssetElementContract $asset): HasLinkContact|AssetElementContract => $asset instanceof HasLinkContact
                ? $asset->link($resolver->get($asset->getLink()))
                : $asset
        );
    }

    public function withVersion(int|string $version): self
    {
        return $this->map(
            static fn (AssetElementContract $asset): HasVersionContact|AssetElementContract => $asset instanceof HasVersionContact
                ? $asset->version($version)
                : $asset
        );
    }

    public function toHtml(): string
    {
        return $this->implode(
            static fn (AssetElementContract $asset) => $asset
                ->toHtml(),
            PHP_EOL
        );
    }
}

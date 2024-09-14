<?php

declare(strict_types=1);

namespace MoonShine\Contracts\AssetManager;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;

/**
 * @template-extends Enumerable<array-key, AssetElementContract>
 *
 * @mixin Collection
 */
interface AssetElementsContract extends Enumerable, Htmlable
{
    public function resolveLinks(AssetResolverContract $resolver): self;

    public function withVersion(int|string $version): self;
}

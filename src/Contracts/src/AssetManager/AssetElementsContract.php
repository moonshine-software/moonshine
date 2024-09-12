<?php

declare(strict_types=1);

namespace MoonShine\Contracts\AssetManager;

use Illuminate\Contracts\Support\Htmlable;
use Traversable;

interface AssetElementsContract extends Htmlable
{
    public function resolveLinks(AssetResolverContract $resolver): self;
}

<?php

declare(strict_types=1);

namespace MoonShine\Contracts\AssetManager;

use Illuminate\Contracts\Support\Htmlable;
use Stringable;

interface AssetElementContract extends Htmlable, Stringable
{
}

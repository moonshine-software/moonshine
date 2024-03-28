<?php

declare(strict_types=1);

namespace MoonShine\AssetManager;

use Illuminate\Contracts\Support\Htmlable;
use Stringable;

interface AssetElement extends Htmlable, Stringable
{
}

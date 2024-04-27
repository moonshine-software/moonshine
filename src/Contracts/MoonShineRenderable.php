<?php

declare(strict_types=1);

namespace MoonShine\Contracts;

use Illuminate\Contracts\Support\CanBeEscapedWhenCastToString;
use JsonSerializable;
use Stringable;

interface MoonShineRenderable extends
    Stringable,
    JsonSerializable,
    CanBeEscapedWhenCastToString
{
    public function render();
}

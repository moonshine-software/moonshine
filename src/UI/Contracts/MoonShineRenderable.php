<?php

declare(strict_types=1);

namespace MoonShine\UI\Contracts;

use Closure;
use Illuminate\Contracts\Support\CanBeEscapedWhenCastToString;
use Illuminate\Contracts\Support\Renderable;
use JsonSerializable;
use Stringable;

interface MoonShineRenderable extends
    Stringable,
    JsonSerializable,
    CanBeEscapedWhenCastToString
{
    public function render(): Renderable|Closure|string;

    public function toStructure(bool $withStates = true): array;

    public function toArray(): array;
}

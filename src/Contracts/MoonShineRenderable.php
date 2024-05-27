<?php

declare(strict_types=1);

namespace MoonShine\Contracts;

use Closure;
use Illuminate\Contracts\Support\CanBeEscapedWhenCastToString;
use Illuminate\Contracts\View\View;
use JsonSerializable;
use Stringable;

interface MoonShineRenderable extends
    Stringable,
    JsonSerializable,
    CanBeEscapedWhenCastToString
{
    public function render(): View|Closure|string;

    public function toStructure(bool $withStates = true): array;

    public function toArray(): array;
}

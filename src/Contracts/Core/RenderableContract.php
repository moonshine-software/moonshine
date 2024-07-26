<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use Closure;
use Illuminate\Contracts\Support\CanBeEscapedWhenCastToString;
use Illuminate\Contracts\Support\Renderable;
use JsonSerializable;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use Stringable;

interface RenderableContract extends
    Stringable,
    JsonSerializable,
    CanBeEscapedWhenCastToString
{
    public function getCore(): CoreContract;

    public function render(): Renderable|Closure|string;

    public function toStructure(bool $withStates = true): array;

    public function toArray(): array;
}

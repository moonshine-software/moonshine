<?php

declare(strict_types=1);

namespace MoonShine\Contracts;

use Stringable;

interface MoonShineRenderable extends Stringable
{
    public function render();
}

<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts;

use Illuminate\Contracts\View\View;

interface Renderable
{
    public function render(): string|View;
}

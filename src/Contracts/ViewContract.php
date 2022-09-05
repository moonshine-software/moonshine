<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts;

use Leeto\MoonShine\ViewComponents\ViewComponents;

interface ViewContract
{
    public function components(): ViewComponents;
}

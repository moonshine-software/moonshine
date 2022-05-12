<?php

namespace Leeto\MoonShine\Controllers;

use JetBrains\PhpStorm\Pure;
use Leeto\MoonShine\Resources\MoonShineUserResource;

class MoonShineUserController extends BaseMoonShineController
{
    #[Pure]
    public function __construct()
    {
        $this->resource = new MoonShineUserResource();
    }
}

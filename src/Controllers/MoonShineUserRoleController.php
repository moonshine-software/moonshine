<?php

namespace Leeto\MoonShine\Controllers;

use JetBrains\PhpStorm\Pure;
use Leeto\MoonShine\Resources\MoonShineUserRoleResource;

class MoonShineUserRoleController extends BaseMoonShineController
{
    #[Pure]
    public function __construct()
    {
        $this->resource = new MoonShineUserRoleResource();
    }
}

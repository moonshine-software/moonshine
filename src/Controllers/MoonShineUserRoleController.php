<?php

namespace Leeto\MoonShine\Controllers;

use Leeto\MoonShine\Resources\MoonShineUserRoleResource;

class MoonShineUserRoleController extends BaseMoonShineController
{
    public function __construct()
    {
        $this->resource = new MoonShineUserRoleResource();
    }
}

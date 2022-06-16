<?php

namespace Leeto\MoonShine\Http\Controllers;

use Leeto\MoonShine\Resources\MoonShineUserRoleResource;

class MoonShineUserRoleController extends MoonShineController
{
    public function __construct()
    {
        $this->resource = new MoonShineUserRoleResource();
    }
}

<?php

namespace Leeto\MoonShine\Controllers;

use Leeto\MoonShine\Resources\MoonShineUserResource;

class MoonShineUserController extends BaseMoonShineController
{
    public function __construct()
    {
        $this->resource = new MoonShineUserResource();
    }
}

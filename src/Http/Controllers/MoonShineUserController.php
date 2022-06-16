<?php

namespace Leeto\MoonShine\Http\Controllers;

use Leeto\MoonShine\Resources\MoonShineUserResource;

class MoonShineUserController extends MoonShineController
{
    public function __construct()
    {
        $this->resource = new MoonShineUserResource();
    }
}

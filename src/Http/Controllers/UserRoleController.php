<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Leeto\MoonShine\MoonShine;
use Leeto\MoonShine\Resources\MoonShineUserRoleResource;

class UserRoleController extends MoonShineCrudController
{
    public function __construct()
    {
        $resourceClass = (string) str(MoonShine::namespace('\Resources\\'))
            ->append('MoonShineUserRoleResource');

        $this->resource = class_exists($resourceClass)
            ? new $resourceClass()
            : new MoonShineUserRoleResource();
    }
}

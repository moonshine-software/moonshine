<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Leeto\MoonShine\MoonShine;

use function str;

class ResourceController extends MoonShineCrudController
{
    public function __construct()
    {
        if (! app()->runningInConsole()) {
            $class = (string) str(request()->route()->getName())->betweenFirst('.', '.')
                ->singular()
                ->ucfirst()
                ->append('Resource')
                ->prepend(MoonShine::namespace('\Resources\\'));

            $this->resource = new $class();
        }
    }
}

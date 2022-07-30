<?php

namespace Leeto\MoonShine\Http\Controllers;

use Leeto\MoonShine\MoonShine;

use function str;

class MoonShineResourceController extends MoonShineController
{
    public function __construct()
    {
        if(!app()->runningInConsole()) {
            $class = (string) str(request()->route()->getName())->betweenFirst('.', '.')
                ->singular()
                ->ucfirst()
                ->append('Resource')
                ->prepend(MoonShine::namespace('\Resources\\'));

            $this->resource = new $class();
        }
    }
}

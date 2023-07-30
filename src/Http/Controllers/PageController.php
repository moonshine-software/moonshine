<?php

namespace MoonShine\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller as BaseController;
use MoonShine\MoonShineRequest;

class PageController extends BaseController
{
    public function __invoke(MoonShineRequest $request): View
    {
        return $request->getPage()->render();
    }
}

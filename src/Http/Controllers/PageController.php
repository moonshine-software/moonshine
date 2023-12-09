<?php

namespace MoonShine\Http\Controllers;

use Illuminate\Contracts\View\View;
use MoonShine\MoonShineRequest;

class PageController extends MoonShineController
{
    public function __invoke(MoonShineRequest $request): View|string
    {
        $page = $request->getPage();

        $item = $page->hasResource() ? $page->getResource() : $page;
        if(!$item->canSee($request)) {
            oops403();
        }

        $page->beforeRender();

        return $page->render();
    }
}

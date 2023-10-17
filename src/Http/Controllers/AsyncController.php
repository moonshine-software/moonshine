<?php

namespace MoonShine\Http\Controllers;

use Illuminate\Contracts\View\View;
use Closure;
use MoonShine\MoonShineRequest;
use Throwable;

class AsyncController extends MoonShineController
{
    /**
     * @throws Throwable
     */
    public function table(MoonShineRequest $request): View|Closure|string
    {
        $page = $request->getPage();

        $table = $page->getComponents()->findTable(request('_component_name'));

        return $table ? $table->render() : '';
    }
}
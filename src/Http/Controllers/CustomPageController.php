<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller as BaseController;
use Leeto\MoonShine\MoonShine;

use Leeto\MoonShine\Resources\CustomPage;
use Symfony\Component\HttpFoundation\Response;

class CustomPageController extends BaseController
{
    public function __invoke(string $alias): Factory|View|Application
    {
        if (app()->runningInConsole()) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $page = app(MoonShine::class)->getPages()
            ->first(fn (CustomPage $page) => $page->alias() === $alias);

        abort_if(! $page, Response::HTTP_NOT_FOUND);

        return view('moonshine::custom_page', [
            'page' => $page,
        ]);
    }
}

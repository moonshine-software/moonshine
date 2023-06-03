<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller as BaseController;
use MoonShine\MoonShine;
use MoonShine\Resources\CustomPage;
use Symfony\Component\HttpFoundation\Response;

class CustomPageController extends BaseController
{
    public function __invoke(string $alias): View
    {
        if (app()->runningInConsole() && ! app()->runningUnitTests()) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $page = MoonShine::getPages()
            ->first(fn (CustomPage $page): bool => $page->alias() === $alias);

        abort_if(! $page, Response::HTTP_NOT_FOUND);

        return view('moonshine::custom_page', [
            'page' => $page,
        ]);
    }
}

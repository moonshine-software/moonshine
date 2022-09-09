<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Leeto\MoonShine\Http\Requests\Resources\ViewComponentsFormRequest;
use Leeto\MoonShine\Traits\Controllers\ApiResponder;
use Leeto\MoonShine\Views\MoonShineView;

final class ViewComponentController extends BaseController
{
    use ApiResponder;

    public function __invoke(ViewComponentsFormRequest $request): JsonResponse
    {
        /* @var MoonShineView $viewClass */
        $viewClass = $request->getViewClass();

        $view = $viewClass::make($request->getResource());

        return $this->jsonResponse(
            $view->resolveComponent($request->getViewComponentClass())
        );
    }
}

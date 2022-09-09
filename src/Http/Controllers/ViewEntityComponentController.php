<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Leeto\MoonShine\Http\Requests\Resources\ViewComponentsFormRequest;
use Leeto\MoonShine\Traits\Controllers\ApiResponder;
use Leeto\MoonShine\Views\CrudFormView;

final class ViewEntityComponentController extends BaseController
{
    use ApiResponder;

    public function __invoke(ViewComponentsFormRequest $request): JsonResponse
    {
        /* @var CrudFormView $viewClass */
        $viewClass = $request->getViewClass();

        $view = $viewClass::make(
            $request->getResource(),
            $request->getValueEntity()
        );

        return $this->jsonResponse(
            $view->resolveComponent($request->getViewComponentClass())
        );
    }
}

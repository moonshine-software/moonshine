<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Leeto\MoonShine\Exceptions\ViewComponentException;
use Leeto\MoonShine\Exceptions\ViewException;
use Leeto\MoonShine\Http\Requests\Resources\ViewComponentsFormRequest;
use Leeto\MoonShine\Traits\Controllers\ApiResponder;
use Leeto\MoonShine\Views\MoonShineView;

final class ViewEntityComponentController extends BaseController
{
    use ApiResponder;

    /**
     * @throws ViewComponentException
     * @throws ViewException
     */
    public function __invoke(ViewComponentsFormRequest $request): JsonResponse
    {
        $viewClass = $request->getViewClass();

        if (!class_exists($viewClass)) {
            throw ViewException::notFound();
        }

        /* @var MoonShineView $viewClass */
        $view = $viewClass::make(
            $request->getResource(),
            $request->entity()
        );

        return $this->jsonResponse(
            $view->resolveComponent($request->getViewComponentClass())
        );
    }
}

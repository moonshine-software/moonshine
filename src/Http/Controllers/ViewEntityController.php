<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Leeto\MoonShine\Exceptions\ViewException;
use Leeto\MoonShine\Http\Requests\Resources\ViewsFormRequest;
use Leeto\MoonShine\Traits\Controllers\ApiResponder;
use Leeto\MoonShine\Views\MoonShineView;

final class ViewEntityController extends BaseController
{
    use ApiResponder;

    /**
     * @throws ViewException
     */
    public function __invoke(ViewsFormRequest $request): JsonResponse
    {
        $viewClass = $request->getViewClass();

        if (!class_exists($viewClass)) {
            throw ViewException::notFound();
        }

        return $this->jsonResponse(
        /* @var MoonShineView $viewClass */
            $viewClass::make($request->getResource(), $request->getValueEntity())
        );
    }
}

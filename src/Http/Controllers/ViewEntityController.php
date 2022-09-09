<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Leeto\MoonShine\Http\Requests\Resources\ViewsFormRequest;
use Leeto\MoonShine\Traits\Controllers\ApiResponder;
use Leeto\MoonShine\Views\MoonShineView;

final class ViewEntityController extends BaseController
{
    use ApiResponder;

    public function __invoke(ViewsFormRequest $request): JsonResponse
    {
        /* @var MoonShineView $viewClass */
        $viewClass = $request->getViewClass();

        return $this->jsonResponse(
            $viewClass::make($request->getResource(), $request->getValueEntity())
        );
    }
}

<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Leeto\MoonShine\ActionsLayer\MakeTableAction;
use Leeto\MoonShine\MoonShine;
use Leeto\MoonShine\Traits\Controllers\ApiResponder;

final class TableController extends BaseController
{
    use ApiResponder;

    public function __invoke(MakeTableAction $tableAction, string $uri): JsonResponse
    {
        return $this->jsonResponse(
            $tableAction(MoonShine::getResourceFromUri($uri))
        );
    }
}

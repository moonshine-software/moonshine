<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Leeto\MoonShine\Actions\Action;
use Leeto\MoonShine\Http\Requests\Resources\ActionFormRequest;
use Leeto\MoonShine\Traits\Controllers\ApiResponder;

final class ActionController extends BaseController
{
    use ApiResponder;

    public function __invoke(ActionFormRequest $request, string $uri): mixed
    {
        $action = collect($request->getResource()->actions())
            ->firstOrFail(fn(Action $action) => $action->uriKey() === $uri);

        return $action->handle($request);
    }
}

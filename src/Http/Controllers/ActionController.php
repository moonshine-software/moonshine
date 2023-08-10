<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use MoonShine\Http\Requests\Resources\ViewAnyFormRequest;

final class ActionController extends BaseController
{
    public function __invoke(ViewAnyFormRequest $request): mixed
    {
        $actions = $request->getResource()->getActions();

        if ($actions->isNotEmpty()) {
            foreach ($actions as $action) {
                if ($action->isTriggered()) {
                    return $action->handle();
                }
            }
        }

        return $request->redirectRoute(
            $request->getResource()->route('index')
        );
    }
}

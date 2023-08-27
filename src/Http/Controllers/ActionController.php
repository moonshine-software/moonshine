<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use MoonShine\Http\Requests\Resources\ViewAnyFormRequest;

final class ActionController extends MoonShineController
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
            $request->getResource()->route('crud.index')
        );
    }
}

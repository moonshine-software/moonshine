<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\Laravel\MoonShineRequest;
use Throwable;

final class ComponentController extends MoonShineController
{
    /**
     * @throws Throwable
     */
    public function __invoke(MoonShineRequest $request): Renderable|string
    {
        $page = $request->getPage();

        $component = $page->getComponents()->findByName(
            $request->getComponentName()
        );

        if (is_null($component)) {
            return '';
        }

        if ($component instanceof TableBuilderContract) {
            $component = $this->responseWithTable($component);
        }

        return $component->render();
    }
}

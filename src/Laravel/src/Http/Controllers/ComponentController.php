<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Controllers;

use MoonShine\Contracts\Core\DependencyInjection\CrudRequestContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use Throwable;

final class ComponentController extends MoonShineController
{
    /**
     * @throws Throwable
     */
    public function __invoke(CrudRequestContract $request): string
    {
        $this->authorizeResourcePage($request);

        $page = $request->getPage();

        $component = $page->getComponents()->findByName(
            $request->getComponentName()
        );

        if (\is_null($component)) {
            return '';
        }

        if ($component instanceof TableBuilderContract) {
            $component = $this->responseWithTable($component);
        }

        if (\is_string($component)) {
            return '';
        }

        return (string) $component;
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Controllers;

use Illuminate\Http\Request;
use MoonShine\Contracts\Core\DependencyInjection\CrudRequestContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Crud\Contracts\WithResponseModifierContract;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PageController extends MoonShineController
{
    public function __invoke(Request $request, CrudRequestContract $crudRequest): PageContract|JsonResponse|Response
    {
        $crudRequest->getResource();

        $page = $crudRequest
            ->getPage()
            ->checkUrl();

        if ($request->wantsJson() && $request->hasHeader('X-MS-Structure')) {
            return $this->structureResponse($page, $request);
        }

        if ($page instanceof WithResponseModifierContract && $page->isResponseModified()) {
            return $page->getModifiedResponse();
        }

        return $page;
    }

    private function structureResponse(PageContract $page, Request $request): JsonResponse
    {
        $withStates = ! $request->hasHeader('X-MS-Without-States');

        $layout = $page->getLayout();
        $layoutComponents = $layout->build();

        if ($request->hasHeader('X-MS-Only-Layout')) {
            return response()->json(
                $layoutComponents->toStructure($withStates)
            );
        }

        if ($request->hasHeader('X-MS-Without-Layout')) {
            return response()->json(
                $page->getComponents()->toStructure($withStates)
            );
        }

        return response()->json(
            $page->toStructure($withStates)
        );
    }
}

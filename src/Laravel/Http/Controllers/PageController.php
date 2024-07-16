<?php

namespace MoonShine\Laravel\Http\Controllers;

use MoonShine\Contracts\Core\PageContract;
use MoonShine\Core\Pages\ViewPage;
use MoonShine\Laravel\MoonShineRequest;
use Symfony\Component\HttpFoundation\JsonResponse;

class PageController extends MoonShineController
{
    public function __invoke(MoonShineRequest $request): PageContract|JsonResponse
    {
        $page = $request
            ->getPage()
            ->checkUrl()
            ->loaded();

        $request->getResource()?->loaded();

        if($request->wantsJson() && $request->hasHeader('X-MS-Structure')) {
            return $this->structureResponse($page, $request);
        }

        return $page;
    }

    private function structureResponse(PageContract $page, MoonShineRequest $request): JsonResponse
    {
        $withStates = ! $request->hasHeader('X-MS-Without-States');

        $layout = $page->getLayout();
        $layoutComponents = $layout->build();

        if($request->hasHeader('X-MS-Only-Layout')) {
            return response()->json(
                $layoutComponents->toStructure($withStates)
            );
        }

        if($request->hasHeader('X-MS-Without-Layout')) {
            return response()->json(
                $page->getComponents()->toStructure($withStates)
            );
        }

        return response()->json(
            $page->toStructure($withStates)
        );
    }
}

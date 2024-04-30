<?php

namespace MoonShine\Http\Controllers;

use MoonShine\MoonShineRequest;
use MoonShine\Pages\Page;
use MoonShine\Pages\ViewPage;
use Symfony\Component\HttpFoundation\JsonResponse;

class PageController extends MoonShineController
{
    public function __invoke(MoonShineRequest $request): Page|JsonResponse
    {
        $page = $request->getPage()->checkUrl();

        if($request->wantsJson()) {
            return $this->structureResponse($page, $request);
        }

        return $page;
    }

    private function structureResponse(Page $page, MoonShineRequest $request): JsonResponse
    {
        $withStates = ! $request->hasHeader('X-MS-Without-States');

        $layout = $page->layout();
        $emptyPage = ViewPage::make();
        $layoutComponents = $layout->build($emptyPage);

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

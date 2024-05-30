<?php

namespace MoonShine\Laravel\Http\Controllers;

use MoonShine\Core\Pages\ViewPage;
use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Laravel\Pages\Page;
use Symfony\Component\HttpFoundation\JsonResponse;

class PageController extends MoonShineController
{
    public function __invoke(MoonShineRequest $request): Page|JsonResponse
    {
        $page = $request->getPage()->checkUrl();

        if($request->wantsJson() && $request->hasHeader('X-MS-Structure')) {
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

<?php

namespace MoonShine\Http\Controllers;

use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\MoonShineRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Throwable;

class AsyncController extends MoonShineController
{
    /**
     * @deprecated will be removed in 3.0
     * @see component
     * @throws Throwable
     */
    public function table(MoonShineRequest $request): View|Closure|string
    {
        $page = $request->getPage();

        $table = $page->getComponents()->findTable(request('_component_name'));

        return $table ? $table->render() : '';
    }

    /**
     * @throws Throwable
     */
    public function component(MoonShineRequest $request): View|Closure|string
    {
        $page = $request->getPage();

        $table = $page->getComponents()->findByName(request('_component_name'));

        return $table ? $table->render() : '';
    }

    /**
     * @throws Throwable
     */
    public function method(MoonShineRequest $request): JsonResponse
    {
        $toast = [
            'type' => 'info',
            'message' => $request->get('message', ''),
        ];

        try {
            $pageOrResource = $request->hasResource()
                ? $request->getResource()
                : $request->getPage();

            $result = $pageOrResource
                ->{$request->get('method')}($request);

            $toast = $request->session()->get('toast', $toast);
        } catch (Throwable $e) {
            report($e);

            $result = $e;
        }

        $request->session()->forget('toast');

        if($result instanceof JsonResponse) {
            return $result;
        }

        return $this->json(
            message: $result instanceof Throwable ? $result->getMessage() : $toast['message'],
            redirect: $result instanceof RedirectResponse ? $result->getTargetUrl() : null,
            messageType: $result instanceof Throwable ? 'error' : $toast['type']
        );
    }
}

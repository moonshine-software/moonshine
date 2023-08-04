<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use MoonShine\Http\Requests\MoonshineFormRequest;
use MoonShine\Http\Requests\Resources\EditFormRequest;
use MoonShine\Http\Requests\Resources\ViewAnyFormRequest;
use MoonShine\MoonShineUI;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

final class ActionController extends BaseController
{
    public function index(ViewAnyFormRequest $request): mixed
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

    public function item(ViewAnyFormRequest $request): RedirectResponse
    {
        return $this->itemActionProcess(
            $request->getResource()->itemActions(),
            $request
        );
    }

    public function form(EditFormRequest $request): RedirectResponse
    {
        return $this->itemActionProcess(
            $request->getResource()->formActions(),
            $request,
            $request->getResource()->getPageAfterSave()
        );
    }

    public function bulk(ViewAnyFormRequest $request): RedirectResponse
    {
        $redirectRoute = $request->redirectRoute(
            $request->getResource()->route('index')
        );

        if (! request('ids')) {
            return $redirectRoute;
        }

        $actions = $request->getResource()->bulkActions();

        abort_if(
            ! $action = $actions[$request->getIndexParameter()] ?? false,
            ResponseAlias::HTTP_NOT_FOUND
        );

        try {
            $items = $request->getResource()->getModel()
                ->newModelQuery()
                ->findMany(
                    request()->str('ids')
                        ->explode(';')
                        ->filter()
                        ->toArray()
                );

            $items->each(fn ($item) => $action->callback($item));
            MoonShineUI::toast($action->message());
        } catch (Throwable $e) {
            throw_if(! app()->isProduction(), $e);
            report_if(app()->isProduction(), $e);

            MoonShineUI::toast(
                $action->getErrorMessage() ?? __('moonshine::ui.saved_error'),
                'error'
            );
        }

        return $redirectRoute;
    }

    private function itemActionProcess(
        array $actions,
        MoonshineFormRequest $request,
        ?string $route = null
    ): RedirectResponse {
        abort_if(
            ! $action = $actions[$request->getIndexParameter()] ?? false,
            ResponseAlias::HTTP_NOT_FOUND
        );

        $redirectRoute = $request->redirectRoute(
            $route ?? $request->getResource()->route('index')
        );

        try {
            $callback = $action->callback($request->getItem());

            if ($callback instanceof RedirectResponse) {
                $redirectRoute = $callback;
            }

            MoonShineUI::toast($action->message());
        } catch (Throwable $e) {
            throw_if(! app()->isProduction(), $e);
            report_if(app()->isProduction(), $e);

            MoonShineUI::toast(
                $action->getErrorMessage() ?? __('moonshine::ui.saved_error'),
                'error'
            );

            return $redirectRoute;
        }

        return $redirectRoute;
    }
}

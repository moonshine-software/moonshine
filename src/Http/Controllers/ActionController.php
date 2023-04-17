<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;
use MoonShine\Http\Requests\Resources\EditFormRequest;
use MoonShine\Http\Requests\Resources\ViewAnyFormRequest;
use MoonShine\Http\Requests\Resources\ViewFormRequest;
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
        abort_if(
            ! $action = $request->getResource()->itemActions()[$request->getIndexParameter()] ?? false,
            ResponseAlias::HTTP_NOT_FOUND
        );

        $redirectRoute = $request->redirectRoute(
            $request->getResource()->route('index')
        );

        try {
            $action->callback($request->getItem());
        } catch (Throwable $e) {
            throw_if(! app()->isProduction(), $e);
            report($e);

            return $redirectRoute
                ->with('alert', trans('moonshine::ui.saved_error'));
        }

        return $redirectRoute
            ->with('alert', $action->message());
    }

    public function form(EditFormRequest $request): RedirectResponse
    {
        abort_if(
            ! $action = $request->getResource()->formActions()[$request->getIndexParameter()] ?? false,
            ResponseAlias::HTTP_NOT_FOUND
        );

        if (! $redirectRoute = $action->getRedirectTo()) {
            $redirectRoute = $request->redirectRoute(
                $request->getResource()->route('index')
            );
        }

        try {
            $action->callback($request->getItem());
        } catch (Throwable $e) {
            throw_if(! app()->isProduction(), $e);
            report($e);

            return $redirectRoute
                ->with('alert', trans('moonshine::ui.saved_error'));
        }

        return $redirectRoute
            ->with('alert', $action->message());
    }

    public function bulk(ViewAnyFormRequest $request): RedirectResponse
    {
        $redirectRoute = $request->redirectRoute(
            $request->getResource()->route('index')
        );

        if (! request('ids')) {
            return $redirectRoute;
        }

        abort_if(
            ! $action = $request->getResource()->bulkActions()[$request->getIndexParameter()] ?? false,
            ResponseAlias::HTTP_NOT_FOUND
        );

        try {
            $items = $request->getResource()->getModel()
                ->newModelQuery()
                ->findMany(explode(';', request('ids')));

            $items->each(fn ($item) => $action->callback($item));
        } catch (Throwable $e) {
            throw_if(! app()->isProduction(), $e);
            report($e);

            return $redirectRoute
                ->with('alert', trans('moonshine::ui.saved_error'));
        }

        return $redirectRoute
            ->with('alert', $action->message());
    }
}

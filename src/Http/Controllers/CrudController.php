<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use MoonShine\Exceptions\ResourceException;
use MoonShine\Http\Requests\MoonshineFormRequest;
use MoonShine\Http\Requests\Resources\DeleteFormRequest;
use MoonShine\Http\Requests\Resources\MassDeleteFormRequest;
use MoonShine\Http\Requests\Resources\StoreFormRequest;
use MoonShine\Http\Requests\Resources\UpdateFormRequest;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CrudController extends MoonShineController
{
    public function __construct()
    {
        $this->middleware(HandlePrecognitiveRequests::class)
            ->only(['store', 'update']);
    }

    /**
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function store(
        StoreFormRequest $request
    ): Response {
        return $this->updateOrCreate($request);
    }

    /**
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function update(
        UpdateFormRequest $request
    ): Response {
        return $this->updateOrCreate($request);
    }

    public function destroy(DeleteFormRequest $request): Response
    {
        $request->getResource()->delete(
            $request->getResource()->getItemOrFail()
        );

        if ($request->ajax()) {
            return $this->json(message: __('moonshine::ui.deleted'));
        }

        $this->toast(
            __('moonshine::ui.deleted'),
            'success'
        );

        return $request->redirectRoute(
            $request->getResource()->redirectAfterDelete()
        );
    }

    public function massDelete(MassDeleteFormRequest $request): Response
    {
        try {
            $request->getResource()->massDelete($request->get('ids'));
        } catch (Throwable $e) {
            throw_if(! app()->isProduction(), $e);
            report_if(app()->isProduction(), $e);

            $this->toast(
                __('moonshine::ui.saved_error'),
                'error'
            );
        }

        if ($request->ajax()) {
            return $this->json(message: __('moonshine::ui.deleted'));
        }

        $this->toast(
            __('moonshine::ui.deleted'),
            'success'
        );

        return $request->redirectRoute(
            $request->getResource()->redirectAfterDelete()
        );
    }

    /**
     * @throws Throwable
     */
    protected function updateOrCreate(
        MoonshineFormRequest $request
    ): Response {
        $resource = $request->getResource();
        $item = $resource->getItemOrInstance();

        $redirectRoute = $resource->redirectAfterSave();

        $validator = $resource->validate($item);

        if ($request->isAttemptingPrecognition()) {
            return response()->json(
                $validator->errors(),
                $validator->fails()
                    ? Response::HTTP_UNPROCESSABLE_ENTITY
                    : Response::HTTP_OK
            );
        }

        if ($validator->fails()) {
            return $redirectRoute
                ->withErrors($validator, 'crud')
                ->withInput();
        }

        try {
            $resource->save($item);
        } catch (ResourceException $e) {
            throw_if(! app()->isProduction(), $e);
            report_if(app()->isProduction(), $e);

            $this->toast(
                __('moonshine::ui.saved_error'),
                'error'
            );

            return $redirectRoute;
        }

        if ($request->ajax()) {
            return $this->json(
                message: __('moonshine::ui.saved'),
                redirect: $item->wasRecentlyCreated && (! $resource->isAsync() && ! $resource->isCreateInModal())
                ? $resource->redirectAfterSave()
                : null
            );
        }

        $this->toast(
            __('moonshine::ui.saved'),
            'success'
        );

        return $request->redirectRoute(
            $resource->redirectAfterSave()
        );
    }
}

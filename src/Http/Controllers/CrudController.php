<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use MoonShine\Http\Requests\MoonshineFormRequest;
use MoonShine\Http\Requests\Resources\DeleteFormRequest;
use MoonShine\Http\Requests\Resources\MassDeleteFormRequest;
use MoonShine\Http\Requests\Resources\StoreFormRequest;
use MoonShine\Http\Requests\Resources\UpdateFormRequest;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class CrudController extends MoonShineController
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
        /* @var ModelResource $resource */
        $resource = $request->getResource();

        $redirectRoute = $request->get('_redirect', $resource->redirectAfterDelete());

        $try = $this->tryOrRedirect(static fn () => $resource->delete(
            $resource->getItemOrFail()
        ), $redirectRoute);

        if ($try instanceof RedirectResponse) {
            return redirect($redirectRoute);
        }

        if ($request->ajax()) {
            return $this->json(
                message: __('moonshine::ui.deleted'),
                redirect: $request->get('_redirect')
            );
        }

        $this->toast(
            __('moonshine::ui.deleted'),
            'success'
        );

        return redirect($redirectRoute);
    }

    public function massDelete(MassDeleteFormRequest $request): Response
    {
        /* @var ModelResource $resource */
        $resource = $request->getResource();

        $redirectRoute = $request->get('_redirect', $resource->redirectAfterDelete());

        if ($this->tryOrRedirect(
                static fn () => $resource->massDelete($request->get('ids', [])),
                $redirectRoute
            ) instanceof RedirectResponse) {
            return redirect($redirectRoute);
        }

        if ($request->ajax()) {
            return $this->json(message: __('moonshine::ui.deleted'));
        }

        $this->toast(
            __('moonshine::ui.deleted'),
            'success'
        );

        return redirect($redirectRoute);
    }

    /**
     * @throws Throwable
     */
    protected function updateOrCreate(
        MoonshineFormRequest $request
    ): Response {
        /*
         * @var ModelResource $resource
         */
        $resource = $request->getResource();
        $item = $resource->getItemOrInstance();

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
            return back()
                ->withErrors($validator, 'crud')
                ->withInput();
        }

        $redirectRoute = static fn ($resource): mixed => $request->get('_redirect', $resource->redirectAfterSave());

        $itemOrRedirect = $this->tryOrRedirect(
            static fn () => $resource->save($item), $redirectRoute($resource)
        );

        if ($itemOrRedirect instanceof RedirectResponse) {
            return redirect($redirectRoute($resource));
        }

        $resource->setItem($itemOrRedirect);

        if ($request->ajax()) {
            $forceRedirect = $request->boolean('_force_redirect')
                ?  $redirectRoute($resource)
                : null;

            return $this->json(
                message: __('moonshine::ui.saved'),
                redirect: $request->get('_redirect', $forceRedirect)
            );
        }

        $this->toast(
            __('moonshine::ui.saved'),
            'success'
        );

        return redirect(
            $redirectRoute($resource)
        );
    }
}

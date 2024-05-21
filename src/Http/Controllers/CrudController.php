<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use MoonShine\Http\Requests\MoonShineFormRequest;
use MoonShine\Http\Requests\Resources\DeleteFormRequest;
use MoonShine\Http\Requests\Resources\MassDeleteFormRequest;
use MoonShine\Http\Requests\Resources\StoreFormRequest;
use MoonShine\Http\Requests\Resources\UpdateFormRequest;
use MoonShine\MoonShineRequest;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class CrudController extends MoonShineController
{
    public function __construct()
    {
        $this->middleware(HandlePrecognitiveRequests::class)
            ->only(['store', 'update']);
    }

    public function index(MoonShineRequest $request): Jsonable
    {
        abort_if(! $request->wantsJson(), 403);

        $resource = $request->getResource();

        if(is_null($resource)) {
            abort(404, 'Resource not found');
        }

        return $resource->itemsToJson(
            $resource->paginate()
        );
    }

    public function show(MoonShineRequest $request): Jsonable
    {
        abort_if(! $request->wantsJson(), 403);

        $resource = $request->getResource();

        if(is_null($resource)) {
            abort(404, 'Resource not found');
        }

        return $resource->itemToJson(
            $resource->getItem()
        );
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
        /* @var \MoonShine\Resources\ModelResource $resource */
        $resource = $request->getResource();

        $redirectRoute = $request->get('_redirect', $resource->redirectAfterDelete());

        try {
            $resource->delete($resource->getItemOrFail());
        } catch (Throwable $e) {
            return $this->reportAndResponse($request->ajax(), $e, $redirectRoute);
        }

        if ($request->ajax() || $request->wantsJson()) {
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
        /* @var \MoonShine\Resources\ModelResource $resource */
        $resource = $request->getResource();

        $redirectRoute = $request->get('_redirect', $resource->redirectAfterDelete());

        try {
            $resource->massDelete($request->get('ids', []));
        } catch (Throwable $e) {
            return $this->reportAndResponse($request->ajax(), $e, $redirectRoute);
        }

        if ($request->ajax() || $request->wantsJson()) {
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
        MoonShineFormRequest $request
    ): Response {
        /* @var \MoonShine\Resources\ModelResource $resource */
        $resource = $request->getResource();
        $item = $resource->getItemOrInstance();

        $redirectRoute = static fn ($resource): mixed => $request->get('_redirect', $resource->redirectAfterSave());

        try {
            $item = $resource->save($item);
        } catch (Throwable $e) {
            return $this->reportAndResponse($request->ajax(), $e, $redirectRoute($resource));
        }

        $resource->setItem($item);

        if ($request->ajax() || $request->wantsJson()) {
            $forceRedirect = $request->boolean('_force_redirect')
                ? $redirectRoute($resource)
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

<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use MoonShine\Laravel\Http\Requests\MoonShineFormRequest;
use MoonShine\Laravel\Http\Requests\Resources\DeleteFormRequest;
use MoonShine\Laravel\Http\Requests\Resources\MassDeleteFormRequest;
use MoonShine\Laravel\Http\Requests\Resources\StoreFormRequest;
use MoonShine\Laravel\Http\Requests\Resources\UpdateFormRequest;
use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Laravel\Notifications\MoonShineNotificationContract;
use MoonShine\Support\Enums\ToastType;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class CrudController extends MoonShineController
{
    public function __construct(
        protected MoonShineNotificationContract $notification,
    ) {
        parent::__construct($notification);

        $this->middleware(HandlePrecognitiveRequests::class)
            ->only(['store', 'update']);
    }

    public function index(MoonShineRequest $request): Jsonable
    {
        abort_if(! $request->wantsJson(), 403);

        $resource = $request->getResource();

        if (is_null($resource)) {
            abort(404, 'Resource not found');
        }

        $resource->setQueryParams(
            request()->only($resource->getQueryParamsKeys())
        );

        return $resource->modifyCollectionResponse(
            $resource->getItems()
        );
    }

    public function show(MoonShineRequest $request): Jsonable
    {
        abort_if(! $request->wantsJson(), 403);

        $resource = $request->getResource();

        if (is_null($resource)) {
            abort(404, 'Resource not found');
        }

        return $resource->modifyResponse(
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
        /* @var \MoonShine\Laravel\Resources\CrudResource $resource */
        $resource = $request->getResource();

        $redirectRoute = $request->input('_redirect', $resource->getRedirectAfterDelete());


        try {
            $resource->delete($resource->getItemOrFail());
        } catch (Throwable $e) {
            return $this->reportAndResponse($request->ajax(), $e, $redirectRoute);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return $this->json(
                message: __('moonshine::ui.deleted'),
                redirect: $request->input('_redirect')
            );
        }

        $this->toast(
            __('moonshine::ui.deleted'),
            ToastType::SUCCESS
        );

        return redirect($redirectRoute);
    }

    public function massDelete(MassDeleteFormRequest $request): Response
    {
        /* @var \MoonShine\Laravel\Resources\CrudResource $resource */
        $resource = $request->getResource();

        $redirectRoute = $request->input('_redirect', $resource->getRedirectAfterDelete());

        try {
            $resource->massDelete($request->input('ids', []));
        } catch (Throwable $e) {
            return $this->reportAndResponse($request->ajax(), $e, $redirectRoute);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return $this->json(
                message: __('moonshine::ui.deleted'),
                redirect: $request->input('_redirect')
            );
        }

        $this->toast(
            __('moonshine::ui.deleted'),
            ToastType::SUCCESS
        );

        return redirect($redirectRoute);
    }

    /**
     * @throws Throwable
     */
    protected function updateOrCreate(
        MoonShineFormRequest $request
    ): Response {
        /* @var \MoonShine\Laravel\Resources\CrudResource $resource */
        $resource = $request->getResource();
        $item = $resource->getItemOrInstance();

        $redirectRoute = static fn ($resource): mixed => $request->input('_redirect', $resource->getRedirectAfterSave());

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
                redirect: $request->input('_redirect', $forceRedirect),
                status: $resource->isRecentlyCreated() ? Response::HTTP_CREATED : Response::HTTP_OK
            );
        }

        $this->toast(
            __('moonshine::ui.saved'),
            ToastType::SUCCESS
        );

        return redirect(
            $redirectRoute($resource)
        );
    }
}

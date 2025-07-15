<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Http\Request;
use MoonShine\Contracts\Core\DependencyInjection\CrudRequestContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Crud\Contracts\Notifications\MoonShineNotificationContract;
use MoonShine\Crud\Resources\CrudResource;
use MoonShine\Laravel\Http\Requests\MoonShineFormRequest;
use MoonShine\Laravel\Http\Requests\Resources\DeleteFormRequest;
use MoonShine\Laravel\Http\Requests\Resources\MassDeleteFormRequest;
use MoonShine\Laravel\Http\Requests\Resources\StoreFormRequest;
use MoonShine\Laravel\Http\Requests\Resources\UpdateFormRequest;
use MoonShine\Support\Enums\ToastType;
use MoonShine\UI\Enums\HtmlMode;
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

    public function index(Request $request, CrudRequestContract $crudRequest): Jsonable
    {
        abort_if(! $request->wantsJson(), 403);

        $resource = $crudRequest->getResource();

        if (\is_null($resource)) {
            abort(404, 'Resource not found');
        }

        $resource->setQueryParams(
            request()->only($resource->getQueryParamsKeys())
        );

        $resource->setActivePage(
            $resource->getIndexPage()
        );

        return $resource->modifyCollectionResponse(
            $resource->getItems()
        );
    }

    public function show(Request $request, CrudRequestContract $crudRequest): Jsonable
    {
        abort_if(! $request->wantsJson(), 403);

        $resource = $crudRequest->getResource();

        if (\is_null($resource)) {
            abort(404, 'Resource not found');
        }

        $resource->setActivePage(
            $resource->getDetailPage()
        );

        return $resource->modifyResponse(
            $resource->getCaster()->cast(
                $resource->getItemOrFail()
            )
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
        /* @var \MoonShine\Crud\Resources\CrudResource $resource */
        $resource = $request->getResource();

        $resource->setActivePage(
            $resource->getIndexPage()
        );

        $redirectRoute = $request->input('_redirect', $resource->getRedirectAfterDelete());

        try {
            $resource->delete(
                $resource->getCaster()->cast(
                    $resource->getItemOrFail()
                )
            );
        } catch (Throwable $e) {
            return $resource->modifyErrorResponse(
                $this->reportAndResponse($request->ajax(), $e, $redirectRoute),
                $e
            );
        }

        if ($request->ajax() || $request->wantsJson()) {
            return $resource->modifyDestroyResponse(
                $this->json(
                    message: __('moonshine::ui.deleted'),
                    redirect: $request->input('_redirect')
                )
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
        /* @var \MoonShine\Crud\Resources\CrudResource $resource */
        $resource = $request->getResource();

        $resource->setActivePage(
            $resource->getIndexPage()
        );

        $redirectRoute = $request->input('_redirect', $resource->getRedirectAfterDelete());

        try {
            $resource->massDelete($request->input('ids', []));
        } catch (Throwable $e) {
            return $resource->modifyErrorResponse(
                $this->reportAndResponse($request->ajax(), $e, $redirectRoute),
                $e
            );
        }

        if ($request->ajax() || $request->wantsJson()) {
            return $resource->modifyMassDeleteResponse(
                $this->json(
                    message: __('moonshine::ui.deleted'),
                    redirect: $request->input('_redirect')
                )
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
        /* @var \MoonShine\Crud\Resources\CrudResource $resource */
        $resource = $request->getResource();
        $item = $resource->getItemOrInstance();

        $resource->setActivePage(
            $resource->getFormPage()
        );

        $redirectRoute = static function (CrudResource $resource) use ($request): ?string {
            if ($request->boolean('_without-redirect')) {
                return null;
            }

            $redirect = $request->input('_redirect', $resource->getRedirectAfterSave());

            if (\is_null($redirect) && ! $resource->isCreateInModal() && $resource->isRecentlyCreated()) {
                return $resource->getFormPageUrl($resource->getCastedData());
            }

            return $redirect;
        };

        try {
            $item = $resource->save(
                $resource->getCaster()->cast(
                    $item
                )
            );
        } catch (Throwable $e) {
            return $resource->modifyErrorResponse(
                $this->reportAndResponse($request->ajax(), $e, $redirectRoute($resource)),
                $e
            );
        }

        $resource->setItem($item->getOriginal());

        if ($request->ajax() || $request->wantsJson()) {
            $data = [];
            $castedData = $resource->getCastedData();

            $resource
                ->getFormFields()
                ->onlyFields()
                ->refreshFields()
                ->fillCloned($castedData?->toArray() ?? [], $castedData)
                ->each(function (FieldContract $field) use (&$data): void {
                    $data['htmlData'][] = [
                        'html' => (string) $field
                            ->resolveRefreshAfterApply()
                            ->render(),
                        'selector' => "[data-field-selector='{$field->getNameDot()}']",
                        'htmlMode' => HtmlMode::OUTER_HTML->value,
                    ];
                });

            return $resource->modifySaveResponse(
                $this->json(
                    message: __('moonshine::ui.saved'),
                    data: $data,
                    redirect: $redirectRoute($resource),
                    status: $resource->isRecentlyCreated() ? Response::HTTP_CREATED : Response::HTTP_OK
                )
            );
        }

        $this->toast(
            __('moonshine::ui.saved'),
            ToastType::SUCCESS
        );

        if (\is_null($redirectRoute($resource))) {
            return back();
        }

        return redirect(
            $redirectRoute($resource)
        );
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;
use MoonShine\Exceptions\ResourceException;
use MoonShine\Http\Requests\MoonshineFormRequest;
use MoonShine\Http\Requests\Resources\DeleteFormRequest;
use MoonShine\Http\Requests\Resources\MassDeleteFormRequest;
use MoonShine\Http\Requests\Resources\StoreFormRequest;
use MoonShine\Http\Requests\Resources\UpdateFormRequest;
use MoonShine\MoonShineUI;
use MoonShine\Pages\Crud\FormPage;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class CrudController extends BaseController
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
    ): JsonResponse|RedirectResponse {
        return $this->updateOrCreate($request);
    }

    /**
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function update(
        UpdateFormRequest $request
    ): JsonResponse|RedirectResponse {
        return $this->updateOrCreate($request);
    }

    public function destroy(DeleteFormRequest $request): RedirectResponse
    {
        $request->getResource()->delete(
            $request->getResource()->getItemOrFail()
        );

        MoonShineUI::toast(
            __('moonshine::ui.deleted'),
            'success'
        );

        return $request->redirectRoute(
            $request->getResource()->redirectAfterDelete()
        );
    }

    public function massDelete(MassDeleteFormRequest $request): RedirectResponse
    {
        try {
            $request->getResource()->massDelete($request->get('ids'));

            MoonShineUI::toast(
                __('moonshine::ui.deleted'),
                'success'
            );
        } catch (Throwable $e) {
            throw_if(! app()->isProduction(), $e);
            report_if(app()->isProduction(), $e);

            MoonShineUI::toast(
                __('moonshine::ui.saved_error'),
                'error'
            );
        }

        return $request->redirectRoute(
            $request->getResource()->redirectAfterDelete()
        );
    }

    /**
     * @throws Throwable
     */
    protected function updateOrCreate(
        MoonshineFormRequest $request
    ): JsonResponse|RedirectResponse {
        $resource = $request->getResource();
        $item = $resource->getItemOrInstance();

        $redirectRoute = $request->redirectRoute(
            to_page(
                $request->getResource(),
                FormPage::class,
                $item->exists ? ['resourceItem' => $item] : []
            )
        );

        $validator = $resource->validate($item);

        if ($request->isAttemptingPrecognition()) {
            return response()->json(
                $validator->errors(),
                $validator->fails()
                    ? ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
                    : ResponseAlias::HTTP_OK
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

            MoonShineUI::toast(
                __('moonshine::ui.saved_error'),
                'error'
            );

            return $redirectRoute;
        }

        MoonShineUI::toast(
            __('moonshine::ui.saved'),
            'success'
        );

        return $request->redirectRoute(
            $resource->redirectAfterSave()
        );
    }
}

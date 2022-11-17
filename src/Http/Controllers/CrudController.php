<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Leeto\MoonShine\Exceptions\ResourceException;
use Leeto\MoonShine\Http\Requests\Resources\CreateFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\DeleteFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\EditFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\MassDeleteFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\StoreFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\UpdateFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\ViewAnyFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\ViewFormRequest;
use Leeto\MoonShine\Traits\Controllers\ApiResponder;
use Leeto\MoonShine\Views\CrudDetailView;
use Leeto\MoonShine\Views\CrudFormView;
use Leeto\MoonShine\Views\CrudIndexView;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

final class CrudController extends BaseController
{
    use ApiResponder;

    public function index(ViewAnyFormRequest $request): JsonResponse
    {
        return $this->jsonResponse([
            'resource' => $request->getResource(),
            'view' => CrudIndexView::make($request->getResource())
        ]);
    }

    public function create(CreateFormRequest $request): JsonResponse
    {
        return $this->jsonResponse([
            'resource' => $request->getResource(),
            'view' => CrudFormView::make($request->getResource())
        ]);
    }

    public function edit(EditFormRequest $request): JsonResponse
    {
        return $this->jsonResponse([
            'resource' => $request->getResource(),
            'view' => CrudFormView::make(
                $request->getResource(),
                $request->getValueEntity()
            )
        ]);
    }

    public function show(ViewFormRequest $request): JsonResponse
    {
        return $this->jsonResponse([
            'resource' => $request->getResource(),
            'view' => CrudDetailView::make(
                $request->getResource(),
                $request->getValueEntity()
            )
        ]);
    }

    public function update(UpdateFormRequest $request): JsonResponse
    {
        try {
            $request->getResource()->update($request->getDataOrFail(), $request->values());
        } catch (ResourceException $e) {
            return $this->jsonErrorMessage(
                !app()->isProduction() ? $e->getMessage() : trans('moonshine::ui.saved_error')
            );
        }

        return $this->jsonSuccessMessage(trans('moonshine::ui.saved'));
    }

    public function store(StoreFormRequest $request): JsonResponse
    {
        try {
            $request->getResource()->create($request->getDataInstance(), $request->values());
        } catch (ResourceException $e) {
            return $this->jsonErrorMessage(
                !app()->isProduction() ? $e->getMessage() : trans('moonshine::ui.saved_error')
            );
        }

        return $this->jsonSuccessMessage(
            trans('moonshine::ui.saved'),
            ResponseAlias::HTTP_CREATED
        );
    }

    public function destroy(DeleteFormRequest $request): JsonResponse
    {
        try {
            $request->getResource()->delete($request->getDataOrFail());
        } catch (ResourceException $e) {
            return $this->jsonErrorMessage(
                !app()->isProduction()
                    ? $e->getMessage()
                    : trans('moonshine::ui.deleted_error')
            );
        }

        return $this->jsonSuccessMessage(
            trans('moonshine::ui.deleted')
        );
    }

    public function massDelete(MassDeleteFormRequest $request): JsonResponse
    {
        try {
            $request->getResource()->massDelete($request->get('ids'));
        } catch (ResourceException $e) {
            return $this->jsonErrorMessage(
                !app()->isProduction()
                    ? $e->getMessage()
                    : trans('moonshine::ui.deleted_error')
            );
        }

        return $this->jsonSuccessMessage(
            trans('moonshine::ui.deleted')
        );
    }
}

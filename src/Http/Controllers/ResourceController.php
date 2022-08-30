<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Leeto\MoonShine\DetailCard\DetailCard;
use Leeto\MoonShine\Exceptions\ResourceException;
use Leeto\MoonShine\Form\Form;
use Leeto\MoonShine\Http\Requests\Resources\CreateFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\DeleteFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\EditFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\MassDeleteFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\StoreFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\UpdateFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\ViewAnyFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\ViewFormRequest;
use Leeto\MoonShine\Http\Responses\ResourceDetailCard;
use Leeto\MoonShine\Http\Responses\ResourceForm;
use Leeto\MoonShine\Http\Responses\ResourceIndex;
use Leeto\MoonShine\Table\Table;
use Leeto\MoonShine\Traits\Controllers\ApiResponder;

final class ResourceController extends BaseController
{
    use ApiResponder;

    /**
     * @throws AuthorizationException
     */
    public function index(ViewAnyFormRequest $request): JsonResponse
    {
        $table = Table::make(
            $request->getResource(),
            $request->getResource()->paginate(),
            $request->getResource()->fieldsCollection()->tableFields(),
        );

        return response()->json(
            ResourceIndex::make(
                $request->getResource(),
                $table
            )
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function create(CreateFormRequest $request): JsonResponse
    {
        $form = Form::make($request->getResource()->fieldsCollection()->formFields())
            ->action($request->getResource()->route('store'))
            ->method('post');

        return $this->jsonResponse(ResourceForm::make($request->getResource(), $form));
    }

    /**
     * @throws AuthorizationException
     */
    public function edit(EditFormRequest $request): JsonResponse
    {
        $item = $request->findModel();

        $form = Form::make($request->getResource()->fieldsCollection()->formFields())
            ->action($request->getResource()->route('update', $item->getKey()))
            ->method('put')
            ->fill($item);

        return $this->jsonResponse(ResourceForm::make($request->getResource(), $form));
    }

    public function show(ViewFormRequest $request): JsonResponse
    {
        $card = DetailCard::make(
            $request->getResource()->fieldsCollection()->detailFields(),
            $request->findModel()
        );

        return $this->jsonResponse(ResourceDetailCard::make($request->getResource(), $card));
    }

    /**
     * @throws AuthorizationException
     */
    public function update(UpdateFormRequest $request): JsonResponse
    {
        try {
            $request->getResource()->update($request->findModel(), $request->values());
        } catch (ResourceException $e) {
            return $this->jsonErrorMessage(
                !app()->isProduction() ? $e->getMessage() : trans('moonshine::ui.saved_error')
            );
        }

        return $this->jsonSuccessMessage(trans('moonshine::ui.saved'));
    }

    /**
     * @throws AuthorizationException
     */
    public function store(StoreFormRequest $request): JsonResponse
    {
        try {
            $request->getResource()->create($request->getModel(), $request->values());
        } catch (ResourceException $e) {
            return $this->jsonErrorMessage(
                !app()->isProduction() ? $e->getMessage() : trans('moonshine::ui.saved_error')
            );
        }

        return $this->jsonSuccessMessage(trans('moonshine::ui.saved'));
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(DeleteFormRequest $request): JsonResponse
    {
        try {
            $request->getResource()->delete($request->findModel());
        } catch (ResourceException $e) {
            return $this->jsonErrorMessage(
                !app()->isProduction() ? $e->getMessage() : trans('moonshine::ui.deleted_error')
            );
        }

        return $this->jsonSuccessMessage(trans('moonshine::ui.deleted'));
    }

    public function massDelete(MassDeleteFormRequest $request): JsonResponse
    {
        try {
            $request->getResource()->massDelete($request->get('ids'));
        } catch (ResourceException $e) {
            return $this->jsonErrorMessage(
                !app()->isProduction() ? $e->getMessage() : trans('moonshine::ui.deleted_error')
            );
        }

        return $this->jsonSuccessMessage(trans('moonshine::ui.deleted'));
    }
}

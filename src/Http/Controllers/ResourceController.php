<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Leeto\MoonShine\Actions\Action;
use Leeto\MoonShine\ActionsLayer\MakeTableAction;
use Leeto\MoonShine\DetailCard\DetailCard;
use Leeto\MoonShine\Exceptions\ResourceException;
use Leeto\MoonShine\Form\Form;
use Leeto\MoonShine\Http\Requests\Resources\ActionFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\CreateFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\DeleteFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\EditFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\MassDeleteFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\StoreFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\UpdateFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\ViewAnyFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\ViewFormRequest;
use Leeto\MoonShine\Traits\Controllers\ApiResponder;
use Leeto\MoonShine\ValueEntities\ModelValueEntityBuilder;
use Leeto\MoonShine\ViewComponents\ViewComponents;
use Leeto\MoonShine\Views\DetailView;
use Leeto\MoonShine\Views\FormView;
use Leeto\MoonShine\Views\IndexView;

final class ResourceController extends BaseController
{
    use ApiResponder;

    public function action(ActionFormRequest $request, string $uri): mixed
    {
        $action = collect($request->getResource()->actions())
            ->firstOrFail(fn(Action $action) => $action->uriKey() === $uri);

        return $action->handle($request);
    }

    public function index(ViewAnyFormRequest $request, MakeTableAction $tableAction): JsonResponse
    {
        return response()->json([
            'resource' => $request->getResource(),
            'view' => IndexView::make(
                //endpoint: 'views/'.$request->getResource()->uriKey(),
                ViewComponents::make([
                    $tableAction($request->getResource())
                ]),
            )
        ]);
    }

    public function create(CreateFormRequest $request): JsonResponse
    {
        $form = Form::make($request->getResource()->fieldsCollection()->formFields())
            ->action($request->getResource()->route('store'))
            ->method('post');

        return $this->jsonResponse(
            FormView::make($request->getResource(), $form),
            Response::HTTP_CREATED
        );
    }

    public function edit(EditFormRequest $request): JsonResponse
    {
        $item = $request->findModel();

        $form = Form::make($request->getResource()->fieldsCollection()->formFields())
            ->action($request->getResource()->route('update', $item->getKey()))
            ->method('put')
            ->fill((new ModelValueEntityBuilder($item))->build());

        return $this->jsonResponse(
            FormView::make($request->getResource(), $form)
        );
    }

    public function show(ViewFormRequest $request): JsonResponse
    {
        $card = DetailCard::make(
            $request->getResource()->fieldsCollection()->detailFields(),
            (new ModelValueEntityBuilder($request->findModel()))->build()
        );

        return $this->jsonResponse(
            DetailView::make($request->getResource(), $card)
        );
    }

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

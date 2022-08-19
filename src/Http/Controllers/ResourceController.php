<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Routing\Redirector;
use Leeto\MoonShine\Builders\ModelValuesBuilder;
use Leeto\MoonShine\Exceptions\ResourceException;
use Leeto\MoonShine\Form;
use Leeto\MoonShine\Http\Requests\Resources\CreateFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\DeleteFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\EditFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\MassDeleteFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\UpdateFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\ViewAnyFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\ViewFormRequest;
use Leeto\MoonShine\ModelArrayAdapter;
use Leeto\MoonShine\Resources\Resource;
use Leeto\MoonShine\Table;

class ResourceController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @throws AuthorizationException
     */
    public function index(ViewAnyFormRequest $request): Factory|View|Response|Application|ResponseFactory|JsonResponse
    {
        $table = Table::make(
            $request->getResource()->paginate(),
            $request->getResource()->indexFields()->toArray()
        );

        return response()->json([
            'title' => $request->getResource()->title(),
            'resource' => [
                'title' => $request->getResource()->title(),
                'uriKey' => $request->getResource()->uriKey(),
                'table' => $table
            ]
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function create(CreateFormRequest $request
    ): View|Factory|Redirector|RedirectResponse|Application|JsonResponse {
        $form = Form::make($request->getResource()->formFields()->toArray())
            ->action($request->getResource()->route('store'))
            ->method('post');

        return view($request->getResource()->createEditView(), [
            'resource' => $request->getResource(),
            'form' => $form,
            'item' => $request->getModel(),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function edit(EditFormRequest $request): View|Factory|Redirector|RedirectResponse|Application|JsonResponse
    {
        $item = $request->findModel();


        # TODO Сделать собственный toArray
        # который также по всем relations будет включать keyName
        dd((new ModelValuesBuilder($item))->build());

        $form = Form::make($request->getResource()->formFields()->toArray())
            ->action($request->getResource()->route('update', $item->getKey()))
            ->method('put')
            ->fill($item->toArray());

        /*return response()->json([
            'resource' => $request->getResource(),
            'form' => $form,
            'item' => $request->findModel(),
        ]);*/

        return view($request->getResource()->createEditView(), [
            'resource' => $request->getResource(),
            'form' => $form,
            'item' => $item,
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function show(ViewFormRequest $request): Redirector|Application|RedirectResponse
    {
        return redirect($request->getResource()->route('index'));
    }

    /**
     * @throws AuthorizationException
     */
    public function update(UpdateFormRequest $request): Factory|View|Redirector|Application|RedirectResponse
    {
        return $this->save(
            $request->findModel(),
            $request->getResource(),
            $request->all()
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function store(CreateFormRequest $request): Factory|View|Redirector|Application|RedirectResponse
    {
        return $this->save(
            $request->findModel(),
            $request->getResource(),
            $request->all()
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(DeleteFormRequest $request): Redirector|Application|RedirectResponse
    {
        try {
            $request->getResource()->delete($request->findModel());
        } catch (ResourceException $e) {
            throw_if(!app()->isProduction(), $e);

            return redirect($request->getResource()->route('index'))
                ->with('alert', trans('moonshine::ui.deleted_error'));
        }

        return redirect($request->getResource()->route('index'))
            ->with('alert', trans('moonshine::ui.deleted'));
    }

    public function massDelete(MassDeleteFormRequest $request): Redirector|Application|RedirectResponse
    {
        try {
            $request->getResource()->massDelete($request->get('ids'));
        } catch (ResourceException $e) {
            throw_if(!app()->isProduction(), $e);

            return redirect($request->getResource()->route('index'))
                ->with('alert', trans('moonshine::ui.deleted_error'));
        }

        return redirect($request->getResource()->route('index'))
            ->with('alert', trans('moonshine::ui.deleted'));
    }

    private function save(Model $item, Resource $resource, array $values): Redirector|RedirectResponse|Application
    {
        try {
            $resource->save($item, $values);
        } catch (ResourceException $e) {
            throw_if(!app()->isProduction(), $e);

            return redirect($resource->route($item->exists ? 'edit' : 'create', $item->getKey()))
                ->with('alert', trans('moonshine::ui.saved_error'));
        }

        return redirect($resource->route('index'))
            ->with('alert', trans('moonshine::ui.saved'));
    }
}

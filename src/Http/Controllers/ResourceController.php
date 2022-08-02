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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Routing\Redirector;
use Leeto\MoonShine\Exceptions\ResourceException;
use Leeto\MoonShine\Form;
use Leeto\MoonShine\Http\Requests\CreateFormRequest;
use Leeto\MoonShine\Http\Requests\DeleteFormRequest;
use Leeto\MoonShine\Http\Requests\UpdateFormRequest;
use Leeto\MoonShine\Http\Requests\ViewAnyFormRequest;
use Leeto\MoonShine\Http\Requests\ViewFormRequest;
use Leeto\MoonShine\MoonShineRequest;
use Leeto\MoonShine\Resources\Resource;

class ResourceController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected Resource $resource;

    public function __construct()
    {
        if (!app()->runningInConsole()) {
            $this->resource = (new MoonShineRequest)->getResource();
        }
    }

    /**
     * @throws AuthorizationException
     */
    public function index(ViewAnyFormRequest $request): Factory|View|Response|Application|ResponseFactory
    {
        return view($this->resource->indexView(), [
            'resource' => $request->getResource(),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function create(CreateFormRequest $request): View|Factory|Redirector|RedirectResponse|Application
    {
        $form = Form::make($request->getResource()->fields())
            ->action($request->getResource()->route('store'))
            ->method('post');

        return view($this->resource->createEditView(), [
            'resource' => $this->resource,
            'form' => $form,
            'item' => $request->getModel(),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function edit(UpdateFormRequest $request): View|Factory|Redirector|RedirectResponse|Application
    {
        $item = $request->findModel();

        $form = Form::make($request->getResource()->formElements()->toArray())
            ->action($request->getResource()->route('update', $item->getKey()))
            ->method('put')
            ->fill($item->toArray());

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
        return redirect($this->resource->route('index'));
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
        if ($request->has('ids')) {
            $request->getModel()
                ->newModelQuery()
                ->whereIn($request->getModel()->getKeyName(), explode(';', $request->get('ids')))
                ->delete();
        } else {
            $request->findModel()->delete();
        }

        return redirect($this->resource->route('index'))
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

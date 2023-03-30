<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Validation\ValidationException;
use Leeto\MoonShine\Exceptions\ResourceException;
use Leeto\MoonShine\Http\Requests\Resources\CreateFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\DeleteFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\EditFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\MassDeleteFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\StoreFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\UpdateFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\ViewAnyFormRequest;
use Leeto\MoonShine\Http\Requests\Resources\ViewFormRequest;
use Leeto\MoonShine\MoonShineRequest;
use Leeto\MoonShine\QueryTags\QueryTag;
use Leeto\MoonShine\Resources\Resource;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class CrudController extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    protected Resource $resource;

    public function __construct()
    {
        $this->middleware(HandlePrecognitiveRequests::class)
            ->only(['store', 'update']);
    }

    /**
     * @throws AuthorizationException|Throwable
     */
    public function index(ViewAnyFormRequest $request): View|string
    {
        $resource = $request->getResource();

        if ($request->hasQueryTag() && $resource->queryTags()) {
            $queryTag = collect($resource->queryTags())
                ->first(fn (QueryTag $tag) => $tag->uri() === $request->getQueryTag());

            $resource->customBuilder($queryTag->builder());
        }

        if (request()->ajax()) {
            abort_if(! $request->isRelatableMode(), ResponseAlias::HTTP_NOT_FOUND);

            $resource->relatable(
                $request->relatedColumn(),
                $request->relatedKey()
            );
        }

        $actions = $resource->getActions();

        $view = view($resource->baseIndexView(), [
            'resource' => $resource,
            'filters' => $resource->filters(),
            'dropdownActions' => $actions->filter(fn ($action) => $action->inDropdown()),
            'lineActions' => $actions->filter(fn ($action) => ! $action->inDropdown()),
            'metrics' => $resource->metrics(),
            'items' => $resource->paginate(),
        ]);

        if (request()->ajax()) {
            $sections = $view->renderSections();

            return $sections['content'] ?? '';
        }

        return $view;
    }

    /**
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function create(CreateFormRequest $request): string|View|RedirectResponse
    {
        return $this->createOrEditView($request);
    }

    /**
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function edit(EditFormRequest $request): string|View|RedirectResponse
    {
        return $this->createOrEditView($request);
    }

    /**
     * @throws Throwable
     */
    public function show(ViewFormRequest $request): string|View|RedirectResponse
    {
        return view($request->getResource()->baseShowView(), [
            'resource' => $request->getResource(),
            'item' => $request->getItem(),
        ]);
    }

    /**
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function update(UpdateFormRequest $request): JsonResponse|View|RedirectResponse
    {
        return $this->updateOrCreate($request);
    }

    /**
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function store(StoreFormRequest $request): JsonResponse|View|RedirectResponse
    {
        return $this->updateOrCreate($request);
    }

    public function destroy(DeleteFormRequest $request): RedirectResponse
    {
        $request->getResource()->delete($request->getItem());

        return $request->redirectRoute($request->getResource()->route('index'))
            ->with('alert', trans('moonshine::ui.deleted'));
    }

    public function massDelete(MassDeleteFormRequest $request): RedirectResponse
    {
        $request->getResource()->massDelete($request->get('ids'));

        return $request->redirectRoute($request->getResource()->route('index'))
            ->with('alert', trans('moonshine::ui.deleted'));
    }

    public function updateColumn(Request $request): Response
    {
        $request->validate([
            'model' => ['required', 'string'],
            'key' => ['required'],
            'field' => ['required'],
            'value' => ['required'],
        ]);

        $class = $request->get('model');

        if (! class_exists($class)) {
            ValidationException::withMessages([
                'model' => 'Model not found',
            ]);
        }

        $model = new $class();

        if (in_array(SoftDeletes::class, class_uses_recursive($model), true)) {
            $model = $model->withTrashed();
        }

        $item = $model->findOrFail($request->get('key'));

        $item->{$request->get('field')} = $request->get('value');
        $item->save();

        return response()->noContent();
    }

    /**
     * @throws Throwable
     */
    protected function updateOrCreate(MoonShineRequest $request): JsonResponse|View|RedirectResponse
    {
        $item = $request->getItemOrInstance();
        $resource = $request->getResource();

        if ($request->isMethod('post') || $request->isMethod('put')) {
            $redirectRoute = $request->redirectRoute(
                $resource->route($item->exists ? 'edit' : 'create', $item->getKey())
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
                    ->withErrors($validator)
                    ->withInput();
            }

            try {
                $resource->save($item);
            } catch (ResourceException $e) {
                throw_if(! app()->isProduction(), $e);
                report($e);

                return $redirectRoute
                    ->with('alert', trans('moonshine::ui.saved_error'));
            }

            return $request->redirectRoute($resource->route('index'))
                ->with('alert', trans('moonshine::ui.saved'));
        }

        return $this->createOrEditView($request);
    }

    /**
     * @throws Throwable
     */
    protected function createOrEditView(MoonShineRequest $request): View|string
    {
        $item = $request->getItemOrInstance();
        $resource = $request->getResource();

        if (! $item->exists && $request->isRelatableMode()) {
            $item = $resource->getModel();
            $item->{$request->relatedColumn()} = $request->relatedKey();
        }

        if (request()->ajax()) {
            $resource->precognitionMode();
        }

        $view = view($resource->baseEditView(), [
            'resource' => $resource,
            'item' => $item,
        ]);

        if (request()->ajax()) {
            $sections = $view->renderSections();

            return $sections['content'] ?? '';
        }

        return $view;
    }
}

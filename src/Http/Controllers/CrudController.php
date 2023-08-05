<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;
use MoonShine\Exceptions\ResourceException;
use MoonShine\Http\Requests\MoonshineFormRequest;
use MoonShine\Http\Requests\Resources\CreateFormRequest;
use MoonShine\Http\Requests\Resources\DeleteFormRequest;
use MoonShine\Http\Requests\Resources\EditFormRequest;
use MoonShine\Http\Requests\Resources\MassDeleteFormRequest;
use MoonShine\Http\Requests\Resources\StoreFormRequest;
use MoonShine\Http\Requests\Resources\UpdateFormRequest;
use MoonShine\Http\Requests\Resources\ViewAnyFormRequest;
use MoonShine\Http\Requests\Resources\ViewFormRequest;
use MoonShine\MoonShineUI;
use MoonShine\QueryTags\QueryTag;
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
     * @throws AuthorizationException|Throwable
     */
    public function index(ViewAnyFormRequest $request): View|string
    {
        $resource = $request->getResource();

        if ($request->hasQueryTag() && $resource->queryTags()) {
            $queryTag = collect($resource->queryTags())
                ->first(
                    fn (QueryTag $tag): bool => $tag->uri() === $request->getQueryTag()
                );

            $resource->customBuilder($queryTag->apply(
                $resource->query()
            ));
        }

        if (request()->ajax()) {
            abort_if(
                ! $request->isRelatableMode(),
                ResponseAlias::HTTP_NOT_FOUND
            );

            $resource->relatable();
        }

        $actions = $resource->getActions();

        try {
            $resources = $resource->isPaginationUsed()
                ? $resource->paginate()
                : $resource->items();

            return $this->viewOrFragment(
                view($resource->baseIndexView(), [
                    'resource' => $resource,
                    'resources' => $resources,
                    'filters' => $resource->filters(),
                    'dropdownActions' => $actions->inDropdown(),
                    'lineActions' => $actions->inLine(),
                    'metrics' => $resource->metrics(),
                ])
            );
        } catch (Throwable $e) {
            throw_if(! app()->isProduction(), $e);
            report_if(app()->isProduction(), $e);

            return view('moonshine::components.alert', [
                'type' => 'error',
                'slot' => app()->isProduction()
                    ? trans('moonshine::ui.saved_error')
                    : $e->getMessage(),
            ]);
        }
    }

    protected function viewOrFragment(View $view): View|string
    {
        if (request()->hasHeader('X-Fragment')) {
            return $view->fragment(request()->header('X-Fragment'));
        }

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
    public function create(
        CreateFormRequest $request
    ): string|View|RedirectResponse {
        return $this->createOrEditView($request);
    }

    /**
     * @throws Throwable
     */
    protected function createOrEditView(MoonshineFormRequest $request): View|string
    {
        $item = $request->getItemOrInstance();
        $resource = $request->getResource();

        if (request()->ajax()) {
            $resource->precognitionMode();
        }

        return $this->viewOrFragment(
            view($resource->baseEditView(), [
                'resource' => $resource,
                'item' => $item,
            ])
        );
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
        return $this->viewOrFragment(
            view($request->getResource()->baseShowView(), [
                'resource' => $request->getResource(),
                'item' => $request->getItem(),
            ])
        );
    }

    /**
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function update(
        UpdateFormRequest $request
    ): JsonResponse|View|RedirectResponse {
        return $this->updateOrCreate($request);
    }

    /**
     * @throws Throwable
     */
    protected function updateOrCreate(
        MoonshineFormRequest $request
    ): JsonResponse|View|RedirectResponse {
        $item = $request->getItemOrInstance();
        $resource = $request->getResource();

        if ($request->isMethod('post') || $request->isMethod('put')) {
            $redirectRoute = $request->redirectRoute(
                $resource->route(
                    $item->exists ? 'edit' : 'create',
                    $item->getKey()
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
                    ->withErrors($validator)
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
                $resource->getPageAfterSave()
            );
        }

        return $this->createOrEditView($request);
    }

    /**
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function store(
        StoreFormRequest $request
    ): JsonResponse|View|RedirectResponse {
        return $this->updateOrCreate($request);
    }

    public function destroy(DeleteFormRequest $request): RedirectResponse
    {
        $request->getResource()->delete($request->getItem());

        MoonShineUI::toast(
            __('moonshine::ui.deleted'),
            'success'
        );

        return $request->redirectRoute(
            $request->getResource()->route('index')
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
            $request->getResource()->route('index')
        );
    }
}

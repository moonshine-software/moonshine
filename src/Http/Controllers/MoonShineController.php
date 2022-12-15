<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Exception;
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
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Routing\Redirector;
use Leeto\MoonShine\Exceptions\ResourceException;
use Leeto\MoonShine\Resources\Resource;

use Throwable;

use function auth;
use function redirect;
use function request;
use function throw_if;
use function trans;
use function view;

class MoonShineController extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    protected Resource $resource;

    public function action($id, $index): RedirectResponse
    {
        $item = $this->resource->getModel()
            ->newModelQuery()
            ->findOrFail($id);

        abort_if(!$action = $this->resource->itemActions()[$index] ?? false, 404);

        try {
            $action->callback($item);
        } catch (Throwable $e) {
            throw_if(!app()->isProduction(), $e);

            return redirect($this->resource->route('index'))
                ->with('alert', trans('moonshine::ui.saved_error'));
        }

        return redirect($this->resource->route('index'))
            ->with('alert', $action->message());
    }

    public function bulk($index): RedirectResponse
    {
        if(!request('ids')) {
            return redirect($this->resource->route('index'));
        }

        abort_if(!$action = $this->resource->bulkActions()[$index] ?? false, 404);

        try {
            $items = $this->resource->getModel()
                ->newModelQuery()
                ->findMany(explode(';', request('ids')));

            $items->each(fn($item) => $action->callback($item));
        } catch (Throwable $e) {
            throw_if(!app()->isProduction(), $e);

            return redirect($this->resource->route('index'))
                ->with('alert', trans('moonshine::ui.saved_error'));
        }

        return redirect($this->resource->route('index'))
            ->with('alert', $action->message());
    }

    /**
     * @throws AuthorizationException
     */
    public function index(): Factory|View|Response|Application|ResponseFactory
    {
        if ($this->resource->isWithPolicy()) {
            $this->authorizeForUser(
                auth(config('moonshine.auth.guard'))->user(),
                'viewAny',
                $this->resource->getModel()
            );
        }

        $actions = $this->resource->getActions();

        if ($actions->isNotEmpty()) {
            foreach ($actions as $action) {
                if ($action->isTriggered()) {
                    return $action->handle();
                }
            }
        }

        return view($this->resource->baseIndexView(), [
            'resource' => $this->resource,
            'filters' => $this->resource->filters(),
            'actions' => $actions,
            'metrics' => $this->resource->metrics(),
            'items' => $this->resource->paginate(),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function create(): View|Factory|Redirector|RedirectResponse|Application
    {
        if ($this->resource->isWithPolicy()) {
            $this->authorizeForUser(
                auth(config('moonshine.auth.guard'))->user(),
                'create',
                $this->resource->getModel()
            );
        }

        if (!in_array('create', $this->resource->getActiveActions())) {
            return redirect($this->resource->route('index'));
        }

        return $this->editView();
    }

    /**
     * @throws AuthorizationException
     */
    public function edit($id): View|Factory|Redirector|RedirectResponse|Application
    {
        if (!in_array('edit', $this->resource->getActiveActions())) {
            return redirect($this->resource->route('index'));
        }

        $item = $this->resource->getModel()
            ->newModelQuery()
            ->findOrFail($id);

        if ($this->resource->isWithPolicy()) {
            $this->authorizeForUser(
                auth(config('moonshine.auth.guard'))->user(),
                'update',
                $item
            );
        }

        $this->resource->setItem($item);

        return $this->editView($item);
    }

    /**
     * @throws AuthorizationException
     */
    public function show($id): Redirector|Application|RedirectResponse
    {
        $item = $this->resource->getModel()
            ->newModelQuery()
            ->findOrFail($id);

        if ($this->resource->isWithPolicy()) {
            $this->authorizeForUser(
                auth(config('moonshine.auth.guard'))->user(),
                'view',
                $item
            );
        }

        return redirect($this->resource->route('index'));
    }

    /**
     * @throws AuthorizationException
     */
    public function update($id, Request $request): Factory|View|Redirector|Application|RedirectResponse
    {
        if (!in_array('edit', $this->resource->getActiveActions())) {
            return redirect($this->resource->route('index'));
        }

        $item = $this->resource->getModel()
            ->newModelQuery()
            ->findOrFail($id);

        if ($this->resource->isWithPolicy()) {
            $this->authorizeForUser(
                auth(config('moonshine.auth.guard'))->user(),
                'update',
                $item
            );
        }

        return $this->save($request, $item);
    }

    /**
     * @throws AuthorizationException
     */
    public function store(Request $request): Factory|View|Redirector|Application|RedirectResponse
    {
        if (!in_array('edit', $this->resource->getActiveActions())
            && !in_array('create', $this->resource->getActiveActions())) {
            return redirect($this->resource->route('index'));
        }

        $item = $this->resource->getModel();

        if ($this->resource->isWithPolicy()) {
            $this->authorizeForUser(
                auth(config('moonshine.auth.guard'))->user(),
                'create',
                $item
            );
        }

        return $this->save($request, $item);
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy($id): Redirector|Application|RedirectResponse
    {
        if (!in_array('delete', $this->resource->getActiveActions())) {
            return redirect($this->resource->route('index'));
        }

        if (request()->has('ids')) {
            if ($this->resource->isWithPolicy()) {
                $this->authorizeForUser(
                    auth(config('moonshine.auth.guard'))->user(),
                    'massDelete',
                    $this->resource->getModel()
                );
            }

            $this->resource->massDelete(
                explode(';', request('ids'))
            );
        } else {
            $item = $this->resource->getModel()
                ->newModelQuery()
                ->findOrFail($id);

            if ($this->resource->isWithPolicy()) {
                $this->authorizeForUser(
                    auth(config('moonshine.auth.guard'))->user(),
                    'delete',
                    $item
                );
            }

            $this->resource->delete($item);
        }

        return redirect($this->resource->route('index'))
            ->with('alert', trans('moonshine::ui.deleted'));
    }

    protected function editView(Model $item = null): Factory|View|Application
    {
        return view($this->resource->baseEditView(), [
            'resource' => $this->resource,
            'item' => $item ?? $this->resource->getModel(),
        ]);
    }

    protected function save(Request $request, Model $item): Factory|View|Redirector|Application|RedirectResponse
    {
        if ($request->isMethod('post') || $request->isMethod('put')) {
            $this->resource->validate($item);

            try {
                $this->resource->save($item);
            } catch (ResourceException $e) {
                throw_if(!app()->isProduction(), $e);

                return redirect($this->resource->route($item->exists ? 'edit' : 'create', $item->getKey()))
                    ->with('alert', trans('moonshine::ui.saved_error'));
            }

            return redirect($this->resource->route('index'))
                ->with('alert', trans('moonshine::ui.saved'));
        }

        return $this->editView($item);
    }
}

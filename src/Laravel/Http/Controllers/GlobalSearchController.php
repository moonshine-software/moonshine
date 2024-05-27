<?php

namespace MoonShine\Laravel\Http\Controllers;

use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Laravel\Scout\HasGlobalSearch;
use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;

class GlobalSearchController extends MoonShineController
{
    /**
     * @throws Throwable
     */
    public function __invoke(MoonShineRequest $request): JsonResponse
    {
        $data = [];

        if (! request()->filled('query')) {
            return response()->json($data);
        }

        foreach (moonshineConfig()->getGlobalSearch() as $model) {
            $data += $this->search($model);
        }

        return response()->json($data);
    }

    /**
     * @template T of HasGlobalSearch
     * @param  class-string<T>  $class
     */
    protected function search(string $class): array
    {
        $builder = $class::search(request('query'));

        return (new $class())
            ->searchableQuery($builder)
            ->get()
            ->ensure(HasGlobalSearch::class)
            ->mapToGroups(fn (HasGlobalSearch $model): array => $this->castResponse($model))
            ->toArray();
    }

    protected function castResponse(HasGlobalSearch $model): array
    {
        $data = $model->toSearchableResponse()->toArray();

        return [$data['group'] => data_forget($data, 'group')];
    }
}

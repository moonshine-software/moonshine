<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use MoonShine\MoonShineRequest;

trait WithOnlySelect
{

    protected bool $onlySelected = false;

    protected ?string $searchColumn = null;

    protected ?Closure $searchQuery = null;

    protected ?Closure $searchValueCallback = null;

    public function searchQuery(): ?Closure
    {
        return $this->searchQuery;
    }

    public function searchValueCallback(): ?Closure
    {
        return $this->searchValueCallback;
    }

    public function searchColumn(): ?string
    {
        return $this->searchColumn;
    }

    public function isOnlySelected(): bool
    {
        return $this->onlySelected;
    }

    public function onlySelected(
        string $relation,
        string $searchColumn = null,
        ?Closure $searchQuery = null,
        ?Closure $searchValueCallback = null
    ): static {
        $this->onlySelected = true;
        $this->searchable = true;
        $this->searchColumn = $searchColumn;
        $this->searchQuery = $searchQuery;
        $this->searchValueCallback = $searchValueCallback;

        $this->valuesQuery = function (Builder $query) use ($relation) {
            $request = app(MoonShineRequest::class);

            if ($request->getId()) {
                $related = $this->getRelated($request->getItem());
                $table = $related->{$relation}()->getRelated()->getTable();
                $key = $related->{$relation}()->getRelated()->getKeyName();

                return $query->whereRelation($relation, "$table.$key", '=', $request->getId());
            }

            return $query->has($relation, '>');
        };

        return $this;
    }

}

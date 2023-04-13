<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\Relationships\BelongsToRelation;
use MoonShine\Contracts\Fields\Relationships\HasRelationship;
use MoonShine\MoonShineRequest;
use MoonShine\Traits\Fields\WithRelationship;

class BelongsTo extends Select implements HasRelationship, BelongsToRelation
{
    use WithRelationship;

    protected bool $onlySelected = false;

    protected ?string $searchColumn = null;

    protected ?Closure $searchQuery = null;

    protected ?Closure $searchValueCallback = null;

    public function isMultiple(): bool
    {
        return false;
    }

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

    public function onlySelected(string $relation, string $searchColumn = null, ?Closure $searchQuery = null, ?Closure $searchValueCallback = null): static
    {
        $this->searchable = true;
        $this->onlySelected = true;
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

    public function save(Model $item): Model
    {
        if ($this->requestValue() === false) {
            if ($this->isNullable()) {
                return $item->{$this->relation()}()
                    ->dissociate();
            }

            return $item;
        }

        $value = $item->{$this->relation()}()
            ->getRelated()
            ->findOrFail($this->requestValue());

        return $item->{$this->relation()}()
            ->associate($value);
    }
}

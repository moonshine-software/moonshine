<?php

declare(strict_types=1);

namespace MoonShine\Filters;

use Closure;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\FormElement;
use MoonShine\Traits\Fields\LinkTrait;

abstract class Filter extends FormElement
{
    use LinkTrait;

    protected ?Closure $queryCallback = null;

    public function customQuery(Closure $callback): static
    {
        $this->queryCallback = $callback;

        return $this;
    }

    public function __invoke(Builder $query, $next): Builder
    {
        return $next($this->getQuery($query));
    }

    public function getQuery(Builder $query): Builder
    {
        if ($this->requestValue() === false) {
            return $query;
        }

        return is_callable($this->queryCallback)
            ? call_user_func(
                $this->queryCallback,
                $query,
                $this->requestValue()
            )
            : $this->resolveQuery($query);
    }

    protected function resolveQuery(Builder $query): Builder
    {
        if ($this->hasRelationship()) {
            $related = $this->getRelated($query->getModel());

            return $query->whereHas(
                $this->relation(),
                function (Builder $q) use ($related): Builder {
                    $table = $q->getModel()->getTable();
                    $id = $related->getKeyName();

                    return $q->whereIn(
                        "$table.$id",
                        is_array($this->requestValue())
                            ? $this->requestValue()
                            : [$this->requestValue()]
                    );
                }
            );
        }

        return $query->where($this->column(), $this->requestValue());
    }

    public function name(string $index = null): string
    {
        return $this->prepareName($index, 'filters');
    }

    public function formViewValue(Model $item): mixed
    {
        return $this->requestValue()
            ?? old($this->nameDot(), false);
    }
}

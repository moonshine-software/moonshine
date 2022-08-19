<?php

declare(strict_types=1);

namespace Leeto\MoonShine;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use JsonSerializable;
use Leeto\MoonShine\Traits\FieldFillValue;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithComponentAttributes;

class Table implements JsonSerializable
{
    use Makeable, WithComponentAttributes, FieldFillValue;

    public function __construct(
        protected LengthAwarePaginator $paginator,
        protected array $fields
    ) {
    }

    public function fields(): array
    {
        return $this->fields;
    }

    public function paginator(): LengthAwarePaginator
    {
        return $this->paginator;
    }

    public function resolveFieldsPaginator(): LengthAwarePaginator
    {
        $this->paginator->getCollection()->transform(function ($values) {
            return [
                'id' => $values->getKey(),
                'fields' => static::fillFields($this->fields(), $values->toArray())
            ];
        });

        return $this->paginator;
    }

    public function jsonSerialize(): array
    {
        return [
            'paginator' => collect($this->paginator())->except(['data']),
            'rows' => $this->resolveFieldsPaginator()->items(),
            'columns' => $this->fields(),
        ];
    }
}

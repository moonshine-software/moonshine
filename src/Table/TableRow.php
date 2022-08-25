<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Table;

use Illuminate\Database\Eloquent\Model;
use JsonSerializable;
use Leeto\MoonShine\Fields\Fields;
use Leeto\MoonShine\Resources\Resource;
use Leeto\MoonShine\Traits\Makeable;

class TableRow implements JsonSerializable
{
    use Makeable;

    public function __construct(
        protected Resource $resource,
        protected Model $values,
        protected Fields $fields
    ) {
    }

    public function resource(): Resource
    {
        return $this->resource;
    }

    public function values(): Model
    {
        return $this->values;
    }

    public function fields(): Fields
    {
        return $this->fields->fillValues($this->values());
    }

    public function id(): int
    {
        return $this->values()->getKey();
    }

    public function policies(): array
    {
        return $this->resource()->policies($this->values());
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id(),
            'fields' => $this->fields(),
            'policies' => $this->policies(),
        ];
    }
}

<?php

declare(strict_types=1);

namespace Leeto\MoonShine\ViewComponents\Table;

use JsonSerializable;
use Leeto\MoonShine\Contracts\EntityContract;
use Leeto\MoonShine\Fields\Fields;
use Leeto\MoonShine\Traits\Makeable;

final class TableRow implements JsonSerializable
{
    use Makeable;

    final public function __construct(
        protected EntityContract $values,
        protected Fields $fields
    ) {
    }

    public function values(): EntityContract
    {
        return $this->values;
    }

    public function fields(): Fields
    {
        return $this->fields->fillValues($this->values());
    }

    public function id(): int|string
    {
        return $this->values()->id();
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id(),
            'fields' => $this->fields(),
            'actions' => $this->values()->actions(),
        ];
    }
}

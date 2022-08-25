<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Table;

use JsonSerializable;
use Leeto\MoonShine\Fields\Field;
use Leeto\MoonShine\Fields\Fields;
use Leeto\MoonShine\Traits\Makeable;

class TableHead implements JsonSerializable
{
    use Makeable;

    public function __construct(
        protected Fields $fields
    ) {
    }

    public function fields(): Fields
    {
        return $this->fields;
    }

    public function jsonSerialize(): array
    {
        return $this->fields()->transform(function (Field $field) {
            return [
                'key' => $field->column(),
                'label' => $field->label(),
                'sortDirection' => 'ASC',
                'sortable' => $field->isSortable(),
                'visible' => true,
            ];
        })->toArray();
    }
}

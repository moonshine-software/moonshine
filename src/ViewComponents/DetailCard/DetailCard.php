<?php

declare(strict_types=1);

namespace Leeto\MoonShine\ViewComponents\DetailCard;

use JsonSerializable;
use Leeto\MoonShine\Contracts\ViewComponentContract;
use Leeto\MoonShine\Decorations\Decoration;
use Leeto\MoonShine\Fields\Field;
use Leeto\MoonShine\Fields\Fields;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Contracts\ValueEntityContract;

final class DetailCard implements ViewComponentContract, JsonSerializable
{
    use Makeable;

    /**
     * @param  Fields<Field|Decoration>  $fields
     * @param  ValueEntityContract  $values
     */
    public function __construct(
        protected Fields $fields,
        protected ValueEntityContract $values
    ) {
    }

    public function fields(): Fields
    {
        return $this->fields;
    }

    public function values(): ValueEntityContract
    {
        return $this->values;
    }

    public function jsonSerialize(): array
    {
        return [
            'fields' => $this->fields()->fillValues($this->values()),
        ];
    }
}

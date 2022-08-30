<?php

declare(strict_types=1);

namespace Leeto\MoonShine\DetailCard;

use Illuminate\Database\Eloquent\Model;
use JsonSerializable;
use Leeto\MoonShine\Decorations\Decoration;
use Leeto\MoonShine\Fields\Field;
use Leeto\MoonShine\Fields\Fields;
use Leeto\MoonShine\Traits\Makeable;

final class DetailCard implements JsonSerializable
{
    use Makeable;

    /**
     * @param  Fields<Field|Decoration>  $fields
     * @param  Model  $values
     */
    public function __construct(protected Fields $fields, protected Model $values)
    {
    }

    public function fields(): Fields
    {
        return $this->fields;
    }

    public function values(): Model
    {
        return $this->values;
    }

    public function jsonSerialize(): array
    {
        return [
            'fields' => $this->fields()
                ->fillValues($this->values()),
        ];
    }
}

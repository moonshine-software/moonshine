<?php

declare(strict_types=1);

namespace Leeto\MoonShine\ViewComponents\DetailCard;

use Leeto\MoonShine\Contracts\ValueEntityContract;
use Leeto\MoonShine\Decorations\Decoration;
use Leeto\MoonShine\Fields\Field;
use Leeto\MoonShine\Fields\Fields;
use Leeto\MoonShine\ViewComponents\MoonShineViewComponent;

final class DetailCard extends MoonShineViewComponent
{
    protected static string $component = 'DetailCardComponent';

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
            ...parent::jsonSerialize(),

            'fields' => $this->fields()->fillValues($this->values()),
        ];
    }
}

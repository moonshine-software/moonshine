<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Carbon\Carbon;
use Leeto\MoonShine\Traits\Fields\DateTrait;
use Leeto\MoonShine\Traits\Fields\WithMask;

class Date extends Field
{
    use DateTrait;
    use WithMask;

    protected static string $component = 'DateField';

    protected string $format = 'Y-m-d H:i:s';

    public function value(): ?string
    {
        if($this->isNullable() && is_null(parent::value())) {
            return null;
        }

        $value = Carbon::parse($this->value);

        if (is_callable($this->valueCallback())) {
            return $this->valueCallback()($value);
        }

        return $value->format($this->getFormat());
    }

    public function requestValue(string $prefix = null): ?Carbon
    {
        if($this->isNullable() && parent::requestValue($prefix) === false) {
            return null;
        }

        return Carbon::parse(parent::requestValue($prefix));
    }
}

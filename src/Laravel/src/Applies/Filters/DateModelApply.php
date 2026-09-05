<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\UI\ApplyContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\UI\Fields\Date;

/**
 * @implements ApplyContract<Date, Builder>
 */
class DateModelApply implements ApplyContract
{
    /* @param \MoonShine\UI\Fields\Date $field */
    public function apply(FieldContract $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            /** @var string|\DateTimeInterface|null $value */
            $value = $field->getRequestValue();
            $query->whereDate($field->getColumn(), $value);
        };
    }
}

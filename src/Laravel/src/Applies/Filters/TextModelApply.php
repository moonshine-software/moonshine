<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\UI\ApplyContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Support\DBOperators;
use MoonShine\UI\Fields\Text;

/**
 * @implements ApplyContract<Text, Builder>
 */
class TextModelApply implements ApplyContract
{
    /* @param \MoonShine\UI\Fields\Text $field */
    public function apply(FieldContract $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            /** @var string|int|float|bool|null $value */
            $value = $field->getRequestValue();
            $query->where($field->getColumn(), DBOperators::byModel($query->getModel())->like(), "%{$value}%");
        };
    }
}

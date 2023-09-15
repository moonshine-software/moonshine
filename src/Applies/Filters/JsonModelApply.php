<?php

declare(strict_types=1);

namespace MoonShine\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\ApplyContract;
use MoonShine\Fields\Field;

class JsonModelApply implements ApplyContract
{
    public function apply(Field $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            $values = collect($field->requestValue())
                ->filter();

            if ($values->isNotEmpty()) {
                $query->where(function (Builder $q) use ($values, $field) {
                    foreach ($values->first() as $key => $value) {
                        if (! empty($value)) {
                            $q->whereJsonContains("{$field->column()}->$key", $value);
                        }
                    }
                });
            }
        };
    }
}

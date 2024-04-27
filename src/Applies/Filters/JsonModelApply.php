<?php

declare(strict_types=1);

namespace MoonShine\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\ApplyContract;
use MoonShine\Fields\Field;

class JsonModelApply implements ApplyContract
{
    /* @param  \MoonShine\Fields\Json  $field */
    public function apply(Field $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            $values = collect($field->requestValue())
                ->filter();

            if ($values->isNotEmpty()) {
                $query->where(function (Builder $q) use ($values, $field): void {
                    $data = array_filter($values->first());

                    if (filled($data)) {
                        $q
                            ->when(
                                ! $field->isKeyOrOnlyValue(),
                                fn (Builder $qq) => $qq->whereJsonContains($field->getColumn(), $data)
                            )
                            ->when(
                                $field->isKeyValue(),
                                fn (Builder $qq) => $qq->where(
                                    $field->getColumn() . '->' . ($data['key'] ?? '*'),
                                    $data['value'] ?? ''
                                )
                            )
                            ->when(
                                $field->isOnlyValue(),
                                fn (Builder $qq) => $qq->whereJsonContains($field->getColumn(), $data['value'] ?? '')
                            );
                    }
                });
            }
        };
    }
}

<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\UI\Contracts\ApplyContract;
use MoonShine\UI\Fields\Field;

class RepeaterModelApply implements ApplyContract
{
    /* @param  \MoonShine\UI\Fields\Json  $field */
    public function apply(Field $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            $values = collect($field->getRequestValue())
                ->filter();

            if ($values->isNotEmpty()) {
                $query->where(static function (Builder $q) use ($values, $field): void {
                    $data = array_filter($values->first());
                    $column = str_replace('.', '->', $field->getColumn());

                    if (filled($data)) {
                        $q
                            ->when(
                                ! $field->isKeyOrOnlyValue(),
                                static fn (Builder $qq) => $qq->whereJsonContains($column, $data)
                            )
                            ->when(
                                $field->isKeyValue(),
                                static fn (Builder $qq) => $qq->where(
                                    $column . '->' . ($data['key'] ?? '*'),
                                    $data['value'] ?? ''
                                )
                            )
                            ->when(
                                $field->isOnlyValue(),
                                static fn (Builder $qq) => $qq->whereJsonContains($column, $data['value'] ?? '')
                            );
                    }
                });
            }
        };
    }
}

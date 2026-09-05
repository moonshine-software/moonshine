<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use MoonShine\Contracts\UI\ApplyContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\UI\Fields\Json;

/**
 * @implements ApplyContract<Json, Builder>
 */
class JsonModelApply implements ApplyContract
{
    /* @param  \MoonShine\UI\Fields\Json  $field */
    public function apply(FieldContract $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            /** @var array<array-key, array<string, mixed>>|false $requestValues */
            $requestValues = $field->getRequestValue();
            $values = Collection::make($requestValues ?: [])->filter();

            if ($values->isNotEmpty()) {
                $query->where(static function (Builder $q) use ($values, $field): void {
                    $data = array_filter($values->first() ?? []);
                    /** @var string|int $key */
                    $key = $data['key'] ?? '*';
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
                                    $column . '->' . $key,
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

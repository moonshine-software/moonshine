<?php

declare(strict_types=1);

namespace MoonShine\UI\Collections;

use Closure;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\TableCellsContract;
use MoonShine\UI\Components\Table\TableTd;

/**
 * @template TKey of array-key
 *
 * @extends Collection<TKey, TableTd>
 */
final class TableCells extends Collection implements TableCellsContract
{
    public function pushFields(FieldsContract $fields, ?Closure $builder = null, int $startIndex = 0): self
    {
        $initialBuilder = $builder;

        /**
         * @var int $index
         * @var FieldContract $field
         */
        foreach ($fields as $field) {
            $attributes = $field->getWrapperAttributes()->jsonSerialize();

            $builder = $attributes !== [] ? static fn (TableTd $td): TableTd => $td->customAttributes(
                $field->getWrapperAttributes()->jsonSerialize()
            ) : $initialBuilder;

            $this->pushCell(
                (string) $field,
                $startIndex,
                $builder,
                ['data-column-selection' => $field->getColumn()]
            );

            $builder = null;
            $startIndex++;
        }

        return $this;
    }

    public function pushCell(Closure|string $content, ?int $index = null, ?Closure $builder = null, array $attributes = []): self
    {
        return $this->push(
            TableTd::make($content, $index)->when(
                ! is_null($builder),
                static fn (TableTd $td) => $builder($td)
            )->customAttributes($attributes)
        );
    }

    public function pushWhen(Closure|bool $condition, Closure $value): self
    {
        if(value($condition) === false) {
            return $this;
        }

        return $this->push(
            value($value)
        );
    }

    public function pushCellWhen(Closure|bool $condition, Closure|string $content, ?int $index = null, ?Closure $builder = null): self
    {
        if(value($condition) === false) {
            return $this;
        }

        return $this->pushCell($content, $index, $builder);
    }
}

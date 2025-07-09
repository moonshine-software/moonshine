<?php

declare(strict_types=1);

namespace MoonShine\UI\Collections;

use Closure;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\UI\Collection\TableCellsContract;
use MoonShine\Contracts\UI\TableCellContract;
use MoonShine\UI\Components\Table\TableTd;

/**
 * @extends Collection<array-key, TableCellContract>
 */
final class TableCells extends Collection implements TableCellsContract
{
    public function pushFields(FieldsContract $fields, ?Closure $builder = null, int $startIndex = 0): self
    {
        $initialBuilder = $builder;

        foreach ($fields as $field) {
            $attributes = $field->getWrapperAttributes()->jsonSerialize();

            $builder = $attributes !== [] ? static fn (TableCellContract $td): TableCellContract => $td->customAttributes(
                $field->getWrapperAttributes()->jsonSerialize()
            ) : $initialBuilder;

            $stickyClass = 'sticky-col';

            $this->pushCell(
                (string) $field,
                $startIndex,
                $builder,
                [
                    'data-column-selection' => $field->getIdentity(),
                    'class' => $field->isStickyColumn() ? $stickyClass : '',
                ]
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
                ! \is_null($builder),
                static fn (TableCellContract $td) => $builder($td)
            )->customAttributes($attributes)
        );
    }

    public function pushWhen(Closure|bool $condition, Closure $value): self
    {
        if (value($condition) === false) {
            return $this;
        }

        return $this->push(
            value($value)
        );
    }

    public function pushCellWhen(Closure|bool $condition, Closure|string $content, ?int $index = null, ?Closure $builder = null): self
    {
        if (value($condition) === false) {
            return $this;
        }

        return $this->pushCell($content, $index, $builder);
    }
}
